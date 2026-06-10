<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\ReportExportService;
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

        $query = Booking::with(['user', 'package', 'payment', 'approver']);

        // Apply date filters based on period (filtering by tour_start_date for reports)
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
                    // Ensure dates are properly parsed and include the full end date
                    $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                    $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                    $query->whereBetween('tour_start_date', [$start, $end]);
                }
                break;
        }

        // Calculate summary statistics before pagination
        $stats = [
            'total_bookings' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total_price'),
            'approved_bookings' => (clone $query)->where('status', 'approved')->count(),
            'pending_bookings' => (clone $query)->where('status', 'pending')->count(),
            'paid_payments' => (clone $query)->whereHas('payment', fn($q) => $q->where('status', 'paid'))->count(),
        ];

        $bookings = $query->latest('tour_start_date')->paginate(10);

        return response()->view('admin.reports', compact('bookings', 'period', 'startDate', 'endDate', 'stats'));
    }

    public function bookings(Request $request, string $format = 'json'): JsonResponse|Response
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Booking::with(['user', 'package', 'payment', 'approver']);

        // Apply date filters based on period (filtering by tour_start_date for reports)
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
                    // Ensure dates are properly parsed and include the full end date
                    $start = \Carbon\Carbon::parse($startDate)->startOfDay();
                    $end = \Carbon\Carbon::parse($endDate)->endOfDay();
                    $query->whereBetween('tour_start_date', [$start, $end]);
                }
                break;
        }

        $bookings = $query->latest('tour_start_date')->get();

        $rows = $bookings->map(function (Booking $booking): array {
            return [
                $booking->booking_number,
                $booking->user?->name ?? 'N/A',
                $booking->user?->email ?? 'N/A',
                $booking->package?->name ?? 'N/A',
                $booking->package?->location ?? 'N/A',
                optional($booking->tour_start_date)->format('Y-m-d') ?? 'N/A',
                optional($booking->tour_end_date)->format('Y-m-d') ?? 'N/A',
                $booking->num_adults,
                $booking->num_children,
                $booking->num_seniors,
                $booking->num_guests,
                ucfirst($booking->status),
                number_format($booking->total_price, 2),
                ucfirst($booking->payment?->status ?? 'unpaid'),
                number_format($booking->payment?->amount ?? 0, 2),
                optional($booking->created_at)->format('Y-m-d H:i') ?? 'N/A',
                optional($booking->approved_at)->format('Y-m-d H:i') ?? 'N/A',
                $booking->special_requests ?? 'None',
            ];
        })->all();

        $headers = [
            'Booking Number',
            'Customer Name',
            'Customer Email',
            'Package Name',
            'Location',
            'Start Date',
            'End Date',
            'Adults',
            'Children',
            'Seniors',
            'Total Guests',
            'Status',
            'Total Price (PHP)',
            'Payment Status',
            'Amount Paid (PHP)',
            'Booked At',
            'Approved At',
            'Special Requests',
        ];

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
                    'paid_payments' => $bookings->filter(fn (Booking $booking) => $booking->payment?->status === 'paid')->count(),
                ],
                'data' => $rows,
            ]);
        }

        return response()->json(['message' => 'Unsupported report format.'], 422);
    }

}
