<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\AuthorizesApiAccess;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    use AuthorizesApiAccess;

    public function index(Request $request): JsonResponse
    {
        $payments = Payment::with('booking')
            ->when(! $this->isAdminRequest($request), function ($query) use ($request) {
                $query->whereHas('booking', function ($bookingQuery) use ($request) {
                    $bookingQuery->where('user_id', $this->requireTourist($request)->id);
                });
            })
            ->latest()
            ->get();

        return response()->json([
            'data' => $payments->map(fn (Payment $payment) => $this->paymentPayload($payment)),
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

        $booking = Booking::findOrFail($validated['booking_id']);
        $this->authorizeBookingAccess($request, $booking);

        if (! $this->isAdminRequest($request)) {
            unset($validated['status'], $validated['paid_at']);
        }

        unset($validated['proof_file']);

        if ($request->hasFile('proof_file')) {
            $validated['proof'] = $this->storeProof($request);
        }

        $payment = Payment::updateOrCreate(
            ['booking_id' => $validated['booking_id']],
            $validated
        );

        if (($payment->status ?? null) === 'paid') {
            $this->syncApprovedBooking($payment->booking_id);
        }

        return response()->json(['data' => $this->paymentPayload($payment->load('booking'))], 201);
    }

    public function show(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizePaymentAccess($request, $payment);

        return response()->json(['data' => $this->paymentPayload($payment->load('booking'))]);
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizePaymentAccess($request, $payment);

        $validated = $this->validatePayment($request, $payment->id, true);

        if (isset($validated['booking_id'])) {
            $booking = Booking::findOrFail($validated['booking_id']);
            $this->authorizeBookingAccess($request, $booking);
        }

        if (! $this->isAdminRequest($request)) {
            unset($validated['status'], $validated['paid_at']);
        }

        unset($validated['proof_file']);

        if ($request->hasFile('proof_file')) {
            $this->deletePublicFile($payment->proof);
            $validated['proof'] = $this->storeProof($request);
        }

        $payment->update($validated);

        if (($payment->status ?? null) === 'paid') {
            $this->syncApprovedBooking($payment->booking_id);
        }

        return response()->json(['data' => $this->paymentPayload($payment->refresh()->load('booking'))]);
    }

    public function uploadProof(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizePaymentAccess($request, $payment);

        $request->validate([
            'proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

        $this->deletePublicFile($payment->proof);

        $payment->update([
            'proof' => $this->storeProof($request),
        ]);

        return response()->json(['data' => $this->paymentPayload($payment->refresh()->load('booking'))]);
    }

    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        $this->requireAdmin($request);

        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully.']);
    }

    private function validatePayment(Request $request, ?int $ignoreId = null, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'booking_id' => [$required, 'exists:bookings,id'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'method' => ['sometimes', 'required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'proof' => ['nullable', 'string', 'max:255'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
            'status' => ['sometimes', 'required', 'in:unpaid,paid,refunded'],
            'paid_at' => ['nullable', 'date'],
        ]);
    }

    private function paymentPayload(Payment $payment): array
    {
        $data = $payment->toArray();
        $data['proof_url'] = $payment->proof_url;
        $data['proof_is_image'] = $payment->proof_is_image;
        $data['proof_display_name'] = $payment->proof_display_name;
        $data['has_uploaded_proof'] = $payment->has_uploaded_proof;

        return $data;
    }

    private function storeProof(Request $request): string
    {
        return $request->file('proof_file')->store('payment-proofs', 'public');
    }

    private function deletePublicFile(?string $path): void
    {
        $path = ltrim((string) $path, '/');
        $path = preg_replace('#^(public/storage/|public/|storage/)#i', '', $path);

        if ($path !== '' && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
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
