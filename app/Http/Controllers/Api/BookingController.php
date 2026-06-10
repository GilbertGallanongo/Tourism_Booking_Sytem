<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Booking::with(['user', 'package', 'payment'])->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateBooking($request);
        $package = TourPackage::findOrFail($validated['tour_package_id']);

        $validated['booking_number'] = $this->bookingNumber();
        $validated['tour_date'] = $validated['tour_date'] ?? now()->toDateString();
        $validated['num_guests'] = $validated['num_guests'] ?? 1;
        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['total_price'] = $validated['total_price'] ?? ($package->price * $validated['num_guests']);
        $validated['user_id'] = $validated['user_id'] ?? $request->user()?->id;

        if (! $validated['user_id']) {
            return response()->json(['message' => 'The user field is required.'], 422);
        }

        $booking = Booking::create($validated);

        // Automatically create a payment record for the booking
        $booking->payment()->create([
            'amount' => $booking->total_price,
            'status' => 'unpaid',
            'method' => 'cash',
        ]);

        return response()->json(['data' => $booking->load(['user', 'package', 'payment'])], 201);
    }

    public function show(Booking $booking): JsonResponse
    {
        return response()->json(['data' => $booking->load(['user', 'package', 'payment', 'approver'])]);
    }

    public function update(Request $request, Booking $booking): JsonResponse
    {
        $validated = $this->validateBooking($request);
        $package = TourPackage::findOrFail($validated['tour_package_id'] ?? $booking->tour_package_id);

        if (! array_key_exists('total_price', $validated)) {
            $validated['total_price'] = ($validated['num_guests'] ?? $booking->num_guests) * $package->price;
        }

        $booking->update($validated);

        return response()->json(['data' => $booking->refresh()->load(['user', 'package', 'payment'])]);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }

    // Enhanced Booking Management Methods

    public function confirm(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->bookingService->confirmBooking($booking, $validated['admin_notes'] ?? '');

            return response()->json([
                'message' => 'Booking confirmed successfully.',
                'data' => $booking->refresh()->load(['user', 'package', 'payment']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
            'refund_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        try {
            $refundPercentage = $validated['refund_percentage'] ?? 100;
            $this->bookingService->cancelBooking(
                $booking,
                $validated['reason'] ?? 'Cancelled via API',
                $refundPercentage
            );

            return response()->json([
                'message' => 'Booking cancelled successfully.',
                'data' => $booking->refresh()->load(['user', 'package', 'payment']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function addServices(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'services' => ['required', 'array'],
            'services.*.name' => ['required', 'string'],
            'services.*.price' => ['required', 'numeric', 'min:0'],
            'services.*.description' => ['nullable', 'string'],
        ]);

        try {
            $this->bookingService->addServices($booking, $validated['services']);

            return response()->json([
                'message' => 'Services added successfully.',
                'data' => $booking->refresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function applyDiscount(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'discount_code' => ['required', 'string'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $this->bookingService->applyDiscount(
                $booking,
                $validated['discount_amount'],
                $validated['discount_code']
            );

            return response()->json([
                'message' => 'Discount applied successfully.',
                'data' => $booking->refresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function addNote(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'in:internal,admin'],
        ]);

        try {
            if ($validated['type'] === 'internal') {
                $this->bookingService->addInternalNote($booking, $validated['note']);
            } else {
                $userName = $request->user()?->name ?? 'API User';
                $this->bookingService->addAdminNote($booking, $validated['note'], $userName);
            }

            return response()->json([
                'message' => 'Note added successfully.',
                'data' => $booking->refresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function updateGuests(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'guests' => ['required', 'array'],
            'guests.*.name' => ['required', 'string', 'max:100'],
            'guests.*.email' => ['nullable', 'email'],
            'guests.*.phone' => ['nullable', 'string'],
            'guests.*.age' => ['nullable', 'integer', 'min:1', 'max:150'],
        ]);

        try {
            $this->bookingService->updateGuestDetails($booking, $validated['guests']);

            return response()->json([
                'message' => 'Guest details updated successfully.',
                'data' => $booking->refresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function getDueReminders(): JsonResponse
    {
        try {
            $reminders = $this->bookingService->getBookingsDueForReminder();

            return response()->json([
                'message' => 'Reminders retrieved successfully.',
                'count' => count($reminders),
                'data' => $reminders->load(['user', 'package']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function markReminderSent(Booking $booking): JsonResponse
    {
        try {
            $this->bookingService->markReminderSent($booking);

            return response()->json([
                'message' => 'Reminder marked as sent.',
                'data' => $booking->refresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function setupPaymentPlan(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'plan_type' => ['required', 'in:full,installment'],
            'installments' => ['required_if:plan_type,installment', 'integer', 'min:2', 'max:12'],
        ]);

        try {
            $this->bookingService->setupPaymentPlan(
                $booking,
                $validated['plan_type'],
                $validated['installments'] ?? 1
            );

            return response()->json([
                'message' => 'Payment plan configured.',
                'data' => $booking->refresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    private function validateBooking(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'tour_package_id' => ['required', 'exists:tour_packages,id'],
            'tour_date' => ['sometimes', 'required', 'date'],
            'tour_start_date' => ['sometimes', 'required', 'date'],
            'tour_end_date' => ['sometimes', 'required', 'date'],
            'num_guests' => ['sometimes', 'required', 'integer', 'min:1'],
            'num_adults' => ['nullable', 'integer', 'min:0'],
            'num_children' => ['nullable', 'integer', 'min:0'],
            'num_seniors' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:pending,approved,cancelled,completed'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'special_requests' => ['nullable', 'string'],
            'approved_by' => ['nullable', 'exists:admins,id'],
            'approved_at' => ['nullable', 'date'],
        ]);
    }

    private function bookingNumber(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_number', $code)->exists());

        return $code;
    }
}

