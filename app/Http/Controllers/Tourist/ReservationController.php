<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\UploadedImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReservationController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['package', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('tourist.reservations.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        $booking->load(['package', 'payment', 'promoPackage']);
        return view('tourist.reservations.show', compact('booking'));
    }

    public function proof(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $payment = $booking->payment()->firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_price,
                'method' => 'cash',
                'status' => 'unpaid',
            ]
        );

        $payment->load(['booking.user']);

        return view('payments.proof', [
            'payment' => $payment,
            'booking' => $booking,
            'backUrl' => route('reservations.show', $booking),
            'backLabel' => 'Back to reservation',
        ]);
    }

    public function edit(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(! $booking->isPending(), 403, 'Only pending reservations can be edited.');

        $booking->load(['package', 'payment', 'promoPackage']);

        return view('tourist.reservations.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(! $booking->isPending(), 403, 'Only pending reservations can be edited.');

        $booking->load(['package', 'promoPackage']);
        $package = $booking->package;
        $promoPackage = $booking->promoPackage;

        $validated = $request->validate([
            'tour_start_date' => ['required', 'date', 'after_or_equal:today'],
            'tour_end_date' => ['required', 'date', 'after:tour_start_date'],
            'num_guests' => ['required', 'integer', 'min:1', 'max:' . $package->max_guests],
            'num_children' => ['nullable', 'integer', 'min:0', 'max:' . $package->max_guests],
            'num_seniors' => ['nullable', 'integer', 'min:0', 'max:' . $package->max_guests],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'tourist_guide' => ['nullable', 'boolean'],
            'services' => ['nullable', 'array'],
            'services.*' => ['in:airport_transfer,travel_insurance,meal_plan'],
        ]);

        $checkIn = Carbon::parse($validated['tour_start_date']);
        $checkOut = Carbon::parse($validated['tour_end_date']);
        $expectedCheckOut = $checkIn->copy()->addDays($package->duration_days);

        if ((int) abs($checkIn->diffInDays($checkOut, false)) !== $package->duration_days) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['tour_end_date' => "Tour end date must be exactly {$package->duration_days} day(s) after tour start for this package. Please select {$expectedCheckOut->format('Y-m-d')}."]);
        }

        if ($promoPackage && $promoPackage->isActive()) {
            $minStartDays = $promoPackage->minStartDays();

            if ($minStartDays > 0 && $checkIn->lt(now()->addDays($minStartDays))) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['tour_start_date' => "This promo requires tour start at least {$minStartDays} days from today."]);
            }
        }

        $totalGuests = (int) $validated['num_guests'];
        $children = (int) ($validated['num_children'] ?? 0);
        $seniors = (int) ($validated['num_seniors'] ?? 0);
        $adults = max(0, $totalGuests - $children - $seniors);

        if (($children + $seniors) > $totalGuests) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Children and seniors cannot be more than the total guests.');
        }

        $availableServices = [
            'airport_transfer' => ['name' => 'Airport transfer', 'price' => 1200],
            'travel_insurance' => ['name' => 'Travel insurance', 'price' => 450],
            'meal_plan' => ['name' => 'Meal plan', 'price' => 650],
        ];

        $serviceItems = [];
        $serviceTotal = 0;

        foreach ($validated['services'] ?? [] as $serviceKey) {
            if (isset($availableServices[$serviceKey])) {
                $serviceItems[] = $availableServices[$serviceKey] + ['key' => $serviceKey];
                $serviceTotal += $availableServices[$serviceKey]['price'];
            }
        }

        $adultAmount = $adults * $package->price;
        $childAmount = $children * ($package->price * 0.5);
        $seniorAmount = $seniors * ($package->price * 0.8);
        $basePrice = $adultAmount + $childAmount + $seniorAmount;
        $touristGuideFee = ! empty($validated['tourist_guide']) ? 1200 : 0;
        $discountAmount = $promoPackage && $promoPackage->isActive()
            ? $basePrice * ($promoPackage->discount_percentage / 100)
            : 0;
        $totalPrice = $basePrice - $discountAmount + $serviceTotal + $touristGuideFee;

        $booking->update([
            'tour_date' => $validated['tour_start_date'],
            'tour_start_date' => $validated['tour_start_date'],
            'tour_end_date' => $validated['tour_end_date'],
            'num_guests' => $totalGuests,
            'num_adults' => $adults,
            'num_children' => $children,
            'num_seniors' => $seniors,
            'base_price' => $basePrice,
            'additional_fees' => $serviceTotal,
            'tourist_guide' => ! empty($validated['tourist_guide']),
            'tourist_guide_fee' => $touristGuideFee,
            'discount_amount' => $discountAmount,
            'discount_code' => $promoPackage && $promoPackage->isActive() ? $promoPackage->name : null,
            'services' => collect($serviceItems),
            'special_requests' => $validated['special_requests'] ?? null,
            'total_price' => $totalPrice,
        ]);

        if ($booking->payment && $booking->payment->status === 'unpaid') {
            $booking->payment->update(['amount' => $totalPrice]);
        }

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'Your reservation has been updated.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->status !== 'pending', 403, 'Only pending bookings can be cancelled.');

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancellation_pending',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()
            ->route('reservations.index')
            ->with('success', "Cancellation request for booking #{$booking->booking_number} submitted. Awaiting admin approval.");
    }

    public function checkIn(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(!$booking->canCheckIn(), 403, 'This reservation cannot be checked in yet.');

        $booking->markAsCheckedIn();

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'You have successfully checked in for your reservation.');
    }

    public function checkOut(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(!$booking->canCheckOut(), 403, 'This reservation cannot be checked out yet.');

        $booking->markAsCheckedOut();

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'You have successfully checked out from your reservation.');
    }

    public function submitPayment(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->isCancelled(), 403, 'Payment submission is not available for cancelled reservations.');

        $tourHasEnded = $booking->isCompleted() || (bool) $booking->tour_ended_at;
        abort_if(! $booking->isConfirmed() && ! $tourHasEnded, 403, 'Please wait for admin confirmation before submitting payment.');

        $payment = $booking->payment()->firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->total_price,
                'method' => 'gcash',
                'status' => 'unpaid',
            ]
        );

        abort_if($payment->status === 'paid', 403, 'This reservation is already paid.');
        abort_if(
            $payment->status === 'unpaid' && ($payment->has_uploaded_proof || $payment->reference_number),
            403,
            'Your payment details have already been submitted for review.'
        );

        $validated = $request->validate([
            'method' => ['required', 'string', 'in:gcash,bank_transfer,cash'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'proof' => ['required_unless:method,cash', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $data = [
            'amount' => $booking->total_price,
            'method' => $validated['method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => 'unpaid',
            'paid_at' => null,
        ];

        if ($request->hasFile('proof')) {
            if ($payment->has_uploaded_proof && ! str_starts_with($payment->proof, 'http')) {
                Storage::disk('public')->delete(UploadedImage::normalize($payment->proof));
            }

            $data['proof'] = $request->file('proof')->store('payment-proofs', 'public');
        }

        $payment->update($data);

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'Payment details submitted. Please wait for admin verification.');
    }
}
