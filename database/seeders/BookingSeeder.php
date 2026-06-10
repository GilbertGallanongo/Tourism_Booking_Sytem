<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $tourist = User::where('email', 'tourist@example.com')->first();
        $juan = User::where('email', 'juan@example.com')->first();
        
        if (!$tourist || !$juan) {
            return;
        }

        $packages = TourPackage::where('status', 'active')->get();
        if ($packages->isEmpty()) {
            return;
        }

        // Past bookings (completed)
        $this->createPastBooking($tourist, $packages[0], '2025-12-15', 'completed');
        $this->createPastBooking($juan, $packages[1], '2025-11-20', 'completed');
        $this->createPastBooking($tourist, $packages[2], '2025-10-10', 'cancelled', 'Emergency cancellation');
        $this->createPastBooking($juan, $packages[3], '2025-09-05', 'completed');
        $this->createPastBooking($tourist, $packages[4], '2025-08-15', 'completed');

        // Recent past (last 30 days)
        $this->createPastBooking($juan, $packages[0], now()->subDays(25)->toDateString(), 'completed');
        $this->createPastBooking($tourist, $packages[1], now()->subDays(15)->toDateString(), 'completed');
        $this->createPastBooking($juan, $packages[2], now()->subDays(10)->toDateString(), 'cancelled', 'Schedule conflict');

        // Current month bookings
        $this->createCurrentBooking($tourist, $packages[3], now()->addDays(5)->toDateString(), 'confirmed');
        $this->createCurrentBooking($juan, $packages[4], now()->addDays(10)->toDateString(), 'confirmed');
        $this->createCurrentBooking($tourist, $packages[0], now()->addDays(15)->toDateString(), 'pending');
        $this->createCurrentBooking($juan, $packages[1], now()->addDays(20)->toDateString(), 'confirmed');

        // Next month bookings
        $this->createFutureBooking($tourist, $packages[2], now()->addMonth()->addDays(5)->toDateString(), 'confirmed');
        $this->createFutureBooking($juan, $packages[3], now()->addMonth()->addDays(12)->toDateString(), 'pending');
        $this->createFutureBooking($tourist, $packages[4], now()->addMonth()->addDays(20)->toDateString(), 'confirmed');
        $this->createFutureBooking($juan, $packages[0], now()->addMonth()->addDays(28)->toDateString(), 'pending');

        // Future bookings (2-3 months ahead)
        $this->createFutureBooking($tourist, $packages[1], now()->addMonths(2)->addDays(5)->toDateString(), 'confirmed');
        $this->createFutureBooking($juan, $packages[2], now()->addMonths(2)->addDays(15)->toDateString(), 'confirmed');
        $this->createFutureBooking($tourist, $packages[3], now()->addMonths(3)->addDays(10)->toDateString(), 'pending');
        $this->createFutureBooking($juan, $packages[4], now()->addMonths(3)->addDays(20)->toDateString(), 'confirmed');

        // Peak season bookings (summer)
        $this->createFutureBooking($tourist, $packages[0], '2026-04-15', 'confirmed');
        $this->createFutureBooking($juan, $packages[1], '2026-04-20', 'confirmed');
        $this->createFutureBooking($tourist, $packages[2], '2026-05-01', 'pending');
        $this->createFutureBooking($juan, $packages[3], '2026-05-10', 'confirmed');
    }

    private function createPastBooking($user, $package, $tourDate, $status, $cancelReason = null)
    {
        $booking = Booking::updateOrCreate(
            ['booking_number' => 'BK-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(4))],
            [
                'user_id' => $user->id,
                'tour_package_id' => $package->id,
                'tour_date' => $tourDate,
                'tour_start_date' => $tourDate,
                'tour_end_date' => date('Y-m-d', strtotime($tourDate . ' +' . $package->duration_days . ' days')),
                'num_guests' => rand(2, 6),
                'num_adults' => rand(2, 4),
                'num_children' => rand(0, 2),
                'num_seniors' => rand(0, 1),
                'status' => $status,
                'base_price' => $package->price,
                'additional_fees' => rand(0, 500),
                'discount_amount' => rand(0, 300),
                'total_price' => $package->price * rand(2, 4),
                'confirmation_code' => 'CONF-' . strtoupper(Str::random(10)),
                'reference_code' => 'REF-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(6)),
                'special_requests' => 'Past booking for time period analysis',
                'cancellation_reason' => $cancelReason,
                'cancelled_at' => $cancelReason ? date('Y-m-d H:i:s', strtotime($tourDate . ' -5 days')) : null,
                'payment_plan' => 'full',
                'payment_installments' => 1,
                'confirmed_at' => date('Y-m-d H:i:s', strtotime($tourDate . ' -10 days')),
                'completed_at' => $status === 'completed' ? date('Y-m-d H:i:s', strtotime($tourDate . ' +' . $package->duration_days . ' days')) : null,
                'tour_started_at' => $status === 'completed' ? date('Y-m-d H:i:s', strtotime($tourDate)) : null,
                'tour_ended_at' => $status === 'completed' ? date('Y-m-d H:i:s', strtotime($tourDate . ' +' . $package->duration_days . ' days')) : null,
                'reminder_sent' => true,
                'reminder_sent_at' => date('Y-m-d H:i:s', strtotime($tourDate . ' -2 days')),
            ]
        );

        if ($status !== 'cancelled') {
            Payment::updateOrCreate(
                ['reference_number' => 'TRX-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(4))],
                [
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'method' => ['gcash', 'bank_transfer', 'cash'][rand(0, 2)],
                    'status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s', strtotime($tourDate . ' -10 days')),
                ]
            );
        }
    }

    private function createCurrentBooking($user, $package, $tourDate, $status)
    {
        $booking = Booking::updateOrCreate(
            ['booking_number' => 'BK-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(4))],
            [
                'user_id' => $user->id,
                'tour_package_id' => $package->id,
                'tour_date' => $tourDate,
                'tour_start_date' => $tourDate,
                'tour_end_date' => date('Y-m-d', strtotime($tourDate . ' +' . $package->duration_days . ' days')),
                'num_guests' => rand(2, 5),
                'num_adults' => rand(2, 3),
                'num_children' => rand(0, 2),
                'num_seniors' => rand(0, 1),
                'status' => $status,
                'base_price' => $package->price,
                'additional_fees' => rand(0, 300),
                'discount_amount' => rand(0, 200),
                'total_price' => $package->price * rand(2, 4),
                'confirmation_code' => $status === 'confirmed' ? 'CONF-' . strtoupper(Str::random(10)) : null,
                'reference_code' => $status === 'confirmed' ? 'REF-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(6)) : null,
                'special_requests' => 'Current month booking',
                'payment_plan' => 'full',
                'payment_installments' => 1,
                'confirmed_at' => $status === 'confirmed' ? now() : null,
                'reminder_sent' => false,
            ]
        );

        if ($status === 'confirmed') {
            Payment::updateOrCreate(
                ['reference_number' => 'TRX-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(4))],
                [
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'method' => ['gcash', 'bank_transfer'][rand(0, 1)],
                    'status' => 'paid',
                    'paid_at' => now()->subDays(rand(1, 5)),
                ]
            );
        }
    }

    private function createFutureBooking($user, $package, $tourDate, $status)
    {
        $booking = Booking::updateOrCreate(
            ['booking_number' => 'BK-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(4))],
            [
                'user_id' => $user->id,
                'tour_package_id' => $package->id,
                'tour_date' => $tourDate,
                'tour_start_date' => $tourDate,
                'tour_end_date' => date('Y-m-d', strtotime($tourDate . ' +' . $package->duration_days . ' days')),
                'num_guests' => rand(2, 8),
                'num_adults' => rand(2, 5),
                'num_children' => rand(0, 3),
                'num_seniors' => rand(0, 2),
                'status' => $status,
                'base_price' => $package->price,
                'additional_fees' => rand(0, 400),
                'discount_amount' => rand(0, 250),
                'total_price' => $package->price * rand(2, 5),
                'confirmation_code' => $status === 'confirmed' ? 'CONF-' . strtoupper(Str::random(10)) : null,
                'reference_code' => $status === 'confirmed' ? 'REF-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(6)) : null,
                'special_requests' => 'Future booking for time period analysis',
                'payment_plan' => 'full',
                'payment_installments' => 1,
                'confirmed_at' => $status === 'confirmed' ? now() : null,
                'reminder_sent' => false,
            ]
        );

        if ($status === 'confirmed') {
            Payment::updateOrCreate(
                ['reference_number' => 'TRX-' . str_replace('-', '', $tourDate) . '-' . strtoupper(Str::random(4))],
                [
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'method' => ['gcash', 'bank_transfer', 'credit_card'][rand(0, 2)],
                    'status' => 'paid',
                    'paid_at' => now(),
                ]
            );
        }
    }
}
