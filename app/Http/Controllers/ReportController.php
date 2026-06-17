<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\ReportExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function __construct(private readonly ReportExportService $exporter)
    {
    }

    public function index(Request $request): Response
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = $this->reportQuery($period, $startDate, $endDate);

        // Calculate summary statistics before pagination
        $stats = [
            'total_bookings' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total_price'),
            'approved_bookings' => (clone $query)->whereIn('status', ['approved', 'confirmed'])->count(),
            'pending_bookings' => (clone $query)->where('status', 'pending')->count(),
            'paid_payments' => (clone $query)->whereHas('payment', fn($q) => $q->where('status', 'paid'))->count(),
        ];

        $bookings = $query->latest('tour_start_date')->paginate(10);
        $reportHeaders = $this->bookingReportHeaders();
        $reportRows = $this->bookingReportRows($bookings->getCollection());

        return response()->view('admin.reports', compact('bookings', 'period', 'startDate', 'endDate', 'stats', 'reportHeaders', 'reportRows'));
    }

    public function bookings(Request $request, string $format = 'json'): JsonResponse|Response
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = $this->reportQuery($period, $startDate, $endDate);

        $bookings = $query->latest('tour_start_date')->get();

        $headers = $this->bookingReportHeaders();
        $rows = $this->bookingReportRows($bookings);

        $format = strtolower($format);

        if (! Storage::disk('local')->exists('reports')) {
            Storage::disk('local')->makeDirectory('reports');
        }

        // Add period to filename for clarity
        $periodSuffix = match($period) {
            'weekly' => '-weekly',
            'monthly' => '-monthly',
            'yearly' => '-yearly',
            'custom' => '-custom',
            default => '',
        };

        if ($format === 'csv') {
            $csv = $this->exporter->csv($headers, $rows);
            $filename = 'bookings-report' . $periodSuffix . '-' . now()->format('YmdHis') . '.csv';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, $csv);

            return response(
                $csv,
                200,
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        }

        if ($format === 'xlsx') {
            $file = $this->exporter->xlsx('Bookings Report' . $periodSuffix, $headers, $rows);
            $filename = 'bookings-report' . $periodSuffix . '-' . now()->format('YmdHis') . '.xlsx';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, file_get_contents($file));
            @unlink($file);

            return response(
                Storage::disk('local')->get($path),
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        }

        if ($format === 'pdf') {
            // Get period label for display
            $periodLabel = match($period) {
                'weekly' => 'This Week',
                'monthly' => 'This Month',
                'yearly' => 'This Year',
                'custom' => 'Custom Range: ' . ($startDate ?? '') . ' to ' . ($endDate ?? ''),
                default => 'All Time',
            };

            $pdf = $this->exporter->pdf('Bookings Report', $headers, $rows, $periodLabel);
            $filename = 'bookings-report' . $periodSuffix . '-' . now()->format('YmdHis') . '.pdf';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, $pdf);

            return response(
                Storage::disk('local')->get($path),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        }

        if ($request->expectsJson() || $format === 'json') {
            return response()->json([
                'summary' => [
                    'total_bookings' => $bookings->count(),
                    'total_revenue' => $bookings->sum('total_price'),
                    'pending_bookings' => $bookings->where('status', 'pending')->count(),
                    'approved_bookings' => $bookings->whereIn('status', ['approved', 'confirmed'])->count(),
                    'paid_payments' => $bookings->filter(fn (Booking $booking) => $booking->payment?->status === 'paid')->count(),
                ],
                'data' => $rows,
            ]);
        }

        return response()->json(['message' => 'Unsupported report format.'], 422);
    }

    private function reportQuery(string $period, ?string $startDate, ?string $endDate): Builder
    {
        $query = Booking::with(['user', 'package', 'payment', 'approver', 'promoPackage']);

        // Reports are filtered by tour_start_date because this reflects the booked tour schedule.
        switch ($period) {
            case 'weekly':
                $query->whereBetween('tour_start_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereBetween('tour_start_date', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'yearly':
                $query->whereBetween('tour_start_date', [now()->startOfYear(), now()->endOfYear()]);
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                    $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                    $query->whereBetween('tour_start_date', [$start, $end]);
                }
                break;
        }

        return $query;
    }

    private function bookingReportHeaders(): array
    {
        return [
            'Booking Number',
            'Booking Reference',
            'Customer Name',
            'Customer Email',
            'Contact Phone',
            'Package Name',
            'Location',
            'Promo Package',
            'Discount Code',
            'Start Date',
            'End Date',
            'Adults',
            'Children',
            'Seniors',
            'Total Guests',
            'Tourist Guide',
            'Services',
            'Base Price (PHP)',
            'Additional Fees (PHP)',
            'Guide Fee (PHP)',
            'Discount (PHP)',
            'Total Price (PHP)',
            'Payment Method',
            'Payment Reference',
            'Proof of Payment',
            'Payment Status',
            'Amount Paid (PHP)',
            'Balance (PHP)',
            'Paid At',
            'Booking Status',
            'Booked At',
            'Approved By',
            'Approved/Confirmed At',
            'Cancelled At',
            'Completed At',
            'Special Requests',
            'Cancellation Reason',
        ];
    }

    private function bookingReportRows(Collection $bookings): array
    {
        return $bookings->map(fn (Booking $booking): array => $this->bookingReportRow($booking))->all();
    }

    private function bookingReportRow(Booking $booking): array
    {
        $payment = $booking->payment;
        $paymentAmount = (float) ($payment?->amount ?? 0);
        $amountPaid = $payment?->status === 'paid' ? $paymentAmount : 0;
        $balance = max(0, (float) $booking->total_price - $amountPaid);
        $paymentMethod = $payment?->method ?? 'not selected';

        if ($payment?->status === 'unpaid' && $paymentMethod === 'cash' && ! $payment->reference_number && ! $payment->proof) {
            $paymentMethod = 'not selected';
        }

        return [
            $booking->booking_number,
            $booking->reference_code ?: 'Pending',
            $booking->user?->name ?? 'N/A',
            $booking->user?->email ?? 'N/A',
            $this->guestDetail($booking, ['contact_phone', 'phone'], 'Not provided'),
            $booking->package?->name ?? 'N/A',
            $booking->package?->location ?? 'N/A',
            $booking->promoPackage
                ? $booking->promoPackage->name . ' (' . $booking->promoPackage->discount_percentage . '%)'
                : 'None',
            $booking->discount_code ?: 'None',
            $this->dateValue($booking->tour_start_date),
            $this->dateValue($booking->tour_end_date),
            $booking->num_adults ?? 0,
            $booking->num_children ?? 0,
            $booking->num_seniors ?? 0,
            $booking->num_guests ?? 0,
            $booking->tourist_guide ? 'Yes' : 'No',
            $this->servicesValue($booking->services),
            $this->money($booking->base_price ?? 0),
            $this->money($booking->additional_fees ?? 0),
            $this->money($booking->tourist_guide_fee ?? 0),
            $this->money($booking->discount_amount ?? 0),
            $this->money($booking->total_price ?? 0),
            $this->statusValue($paymentMethod),
            $payment?->reference_number ?: 'Not provided',
            $payment?->proof_display_name ?? 'None',
            $this->statusValue($payment?->status ?? 'unpaid'),
            $this->money($amountPaid),
            $this->money($balance),
            $this->dateTimeValue($payment?->paid_at),
            $this->statusValue($booking->status),
            $this->dateTimeValue($booking->created_at),
            $booking->approver?->name ?? 'N/A',
            $this->dateTimeValue($booking->approved_at ?? $booking->confirmed_at),
            $this->dateTimeValue($booking->cancelled_at),
            $this->dateTimeValue($booking->completed_at),
            $booking->special_requests ?: 'None',
            $booking->cancellation_reason ?: 'None',
        ];
    }

    private function guestDetail(Booking $booking, array $keys, string $default): string
    {
        $details = $booking->guest_details;

        if (! $details) {
            return $default;
        }

        if ($details instanceof Collection) {
            $details = $details->all();
        }

        foreach ($keys as $key) {
            $value = $details[$key] ?? null;
            if ($value) {
                return (string) $value;
            }
        }

        return $default;
    }

    private function servicesValue($services): string
    {
        if (! $services || count($services) === 0) {
            return 'None';
        }

        return collect($services)
            ->map(function ($service): string {
                if (is_array($service)) {
                    $name = $service['name'] ?? $service['key'] ?? 'Service';
                    $price = isset($service['price']) ? ' - PHP ' . $this->money($service['price']) : '';

                    return $name . $price;
                }

                return ucwords(str_replace('_', ' ', (string) $service));
            })
            ->implode('; ');
    }

    private function statusValue(?string $value): string
    {
        return $value ? ucwords(str_replace('_', ' ', $value)) : 'N/A';
    }

    private function dateValue($date): string
    {
        return $date ? $date->format('Y-m-d') : 'N/A';
    }

    private function dateTimeValue($date): string
    {
        return $date ? $date->format('Y-m-d H:i') : 'N/A';
    }

    private function money($value): string
    {
        return number_format((float) $value, 2);
    }

}
