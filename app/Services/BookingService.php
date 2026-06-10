<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Create a new booking with all necessary initialization
     */
    public function createBooking(array $data): Booking
    {
        $data['booking_number'] = $this->generateBookingNumber();
        $data['confirmation_code'] = $this->generateConfirmationCode();
        $data['reference_code'] = $this->generateReferenceCode();
        $data['status'] = $data['status'] ?? 'pending';

        // If tour start date is provided but tour_date is not, use the tour start date as the primary booking date
        if (!isset($data['tour_date']) && isset($data['tour_start_date'])) {
            $data['tour_date'] = $data['tour_start_date'];
        }

        // Calculate base price if not provided
        if (!isset($data['base_price']) && isset($data['tour_package_id'])) {
            $package = \App\Models\TourPackage::find($data['tour_package_id']);
            $data['base_price'] = $package->price * ($data['num_guests'] ?? 1);
        }

        // Apply promo discount if promo_package_id is provided
        if (isset($data['promo_package_id']) && $data['promo_package_id']) {
            $promoPackage = \App\Models\PromoPackage::find($data['promo_package_id']);
            if ($promoPackage && $promoPackage->isActive()) {
                $discountAmount = ($data['base_price'] * $promoPackage->discount_percentage) / 100;
                $data['discount_amount'] = $discountAmount;
                $data['discount_code'] = $promoPackage->name;
            }
        }

        // Calculate total price
        $data['total_price'] = $data['total_price'] ?? $this->calculateTotal(
            $data['base_price'] ?? 0,
            ($data['additional_fees'] ?? 0) + ($data['tourist_guide_fee'] ?? 0),
            $data['discount_amount'] ?? 0
        );

        $booking = Booking::create($data);

        // Automatically create a payment record for the booking
        $booking->payment()->create([
            'amount' => $booking->total_price,
            'status' => 'unpaid',
            'method' => 'cash',
        ]);

        return $booking;
    }

    /**
     * Confirm a pending booking
     */
    public function confirmBooking(Booking $booking, string $adminNotes = ''): void
    {
        $booking->markAsConfirmed();

        if ($adminNotes) {
            $booking->update(['admin_notes' => $adminNotes]);
        }

        // Create payment record
        $this->createPaymentForBooking($booking);
    }

    /**
     * Cancel a booking with refund
     */
    public function cancelBooking(Booking $booking, string $reason = '', float $refundPercentage = 100): bool
    {
        if (!$booking->canBeCancelled()) {
            return false;
        }

        $refundAmount = ($booking->total_price * $refundPercentage) / 100;
        $booking->markAsCancelled($reason, $refundAmount);

        // Update payment to refunded
        if ($booking->payment) {
            $booking->payment->update([
                'status' => 'refunded',
            ]);
        }

        return true;
    }

    /**
     * Complete a booking (after tour is finished)
     */
    public function completeBooking(Booking $booking): void
    {
        $booking->markAsCompleted();
    }

    /**
     * Apply discount to booking
     */
    public function applyDiscount(Booking $booking, float $discountAmount, string $discountCode = ''): void
    {
        $currentTotal = $booking->total_price;
        $newDiscount = min($discountAmount, $currentTotal);

        $booking->update([
            'discount_amount' => $newDiscount,
            'discount_code' => $discountCode,
            'total_price' => $this->calculateTotal(
                $booking->base_price,
                ($booking->additional_fees ?? 0) + ($booking->tourist_guide_fee ?? 0),
                $newDiscount
            ),
        ]);
    }

    /**
     * Add additional services to booking
     */
    public function addServices(Booking $booking, array $services): void
    {
        $totalServiceFee = 0;

        foreach ($services as $service) {
            $totalServiceFee += $service['price'] ?? 0;
        }

        $booking->update([
            'services' => collect($services),
            'additional_fees' => $booking->additional_fees + $totalServiceFee,
            'total_price' => $this->calculateTotal(
                $booking->base_price,
                $booking->additional_fees + $totalServiceFee,
                $booking->discount_amount
            ),
        ]);
    }

    /**
     * Update guest details (passenger information)
     */
    public function updateGuestDetails(Booking $booking, array $guestDetails): void
    {
        $booking->update(['guest_details' => collect($guestDetails)]);
    }

    /**
     * Add internal note to booking
     */
    public function addInternalNote(Booking $booking, string $note): void
    {
        $currentNotes = $booking->internal_notes ?? '';
        $newNote = "[".\Carbon\Carbon::now()->format('Y-m-d H:i')."] " . $note;
        $updatedNotes = $currentNotes ? $currentNotes . "\n" . $newNote : $newNote;

        $booking->update(['internal_notes' => $updatedNotes]);
    }

    /**
     * Add admin note to booking
     */
    public function addAdminNote(Booking $booking, string $note, string $adminName = 'Admin'): void
    {
        $currentNotes = $booking->admin_notes ?? '';
        $newNote = "[".\Carbon\Carbon::now()->format('Y-m-d H:i')."] $adminName: " . $note;
        $updatedNotes = $currentNotes ? $currentNotes . "\n" . $newNote : $newNote;

        $booking->update(['admin_notes' => $updatedNotes]);
    }

    /**
     * Mark reminder as sent
     */
    public function markReminderSent(Booking $booking): void
    {
        $booking->update([
            'reminder_sent' => true,
            'reminder_sent_at' => now(),
        ]);
    }

    /**
     * Get bookings due for reminder (7 days before tour)
     */
    public function getBookingsDueForReminder()
    {
        return Booking::where('reminder_sent', false)
            ->where('status', 'confirmed')
            ->whereBetween('tour_date', [
                now()->toDateString(),
                now()->addDays(7)->toDateString(),
            ])
            ->get();
    }

    /**
     * Setup payment plan for booking
     */
    public function setupPaymentPlan(Booking $booking, string $planType = 'full', int $installments = 1): void
    {
        $booking->update([
            'payment_plan' => $planType,
            'payment_installments' => $installments,
        ]);
    }

    // Private helper methods

    private function generateBookingNumber(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_number', $code)->exists());

        return $code;
    }

    private function generateConfirmationCode(): string
    {
        return 'CONF-' . strtoupper(Str::random(10));
    }

    private function generateReferenceCode(): string
    {
        return 'REF-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    private function calculateTotal(float $base, float $fees, float $discount): float
    {
        return max(0, ($base + $fees) - $discount);
    }

    private function createPaymentForBooking(Booking $booking): Payment
    {
        return Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_price,
                'method' => 'pending',
                'status' => 'unpaid',
                'reference_number' => $booking->reference_code,
            ]
        );
    }
}
