<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\UploadedImage;
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
        $booking->load(['package', 'payment']);
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
