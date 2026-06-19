<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

trait AuthorizesApiAccess
{
    private function isAdminRequest(Request $request): bool
    {
        $user = $request->user();

        return $user instanceof Admin
            || ($user instanceof User && $user->isAdmin());
    }

    private function touristUser(Request $request): ?User
    {
        $user = $request->user();

        if ($user instanceof User && $user->isTourist() && ! $user->isGuest()) {
            return $user;
        }

        return null;
    }

    private function requireAdmin(Request $request): void
    {
        abort_unless($this->isAdminRequest($request), 403, 'Admin API token required.');
    }

    private function requireTourist(Request $request): User
    {
        $user = $this->touristUser($request);

        abort_unless($user, 403, 'Registered tourist API token required.');

        return $user;
    }

    private function authorizeBookingAccess(Request $request, Booking $booking): void
    {
        $tourist = $this->touristUser($request);

        abort_unless(
            $this->isAdminRequest($request) || ($tourist && $booking->user_id === $tourist->id),
            403,
            'You are not allowed to access this booking.'
        );
    }

    private function authorizePaymentAccess(Request $request, Payment $payment): void
    {
        $payment->loadMissing('booking');
        $tourist = $this->touristUser($request);

        abort_unless(
            $this->isAdminRequest($request) || ($tourist && $payment->booking?->user_id === $tourist->id),
            403,
            'You are not allowed to access this payment.'
        );
    }
}
