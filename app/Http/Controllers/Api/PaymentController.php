<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Payment::with('booking')->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayment($request);

        if (! isset($validated['amount'], $validated['method'])) {
            return response()->json([
                'message' => 'The amount and method fields are required.',
            ], 422);
        }

        $payment = Payment::updateOrCreate(
            ['booking_id' => $validated['booking_id']],
            $validated
        );

        if (($payment->status ?? null) === 'paid') {
            $this->syncApprovedBooking($payment->booking_id);
        }

        return response()->json(['data' => $payment->load('booking')], 201);
    }

    public function show(Payment $payment): JsonResponse
    {
        return response()->json(['data' => $payment->load('booking')]);
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $validated = $this->validatePayment($request, $payment->id);
        $payment->update($validated);

        if (($payment->status ?? null) === 'paid') {
            $this->syncApprovedBooking($payment->booking_id);
        }

        return response()->json(['data' => $payment->refresh()->load('booking')]);
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully.']);
    }

    private function validatePayment(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'method' => ['sometimes', 'required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'proof' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'in:unpaid,paid,refunded'],
            'paid_at' => ['nullable', 'date'],
        ]);
    }

    private function syncApprovedBooking(int $bookingId): void
    {
        $booking = Booking::find($bookingId);

        if ($booking) {
            $booking->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        }
    }
}
