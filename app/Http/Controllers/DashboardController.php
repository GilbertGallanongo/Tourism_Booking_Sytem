<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\FamousTouristSpot;
use App\Models\Payment;
use App\Models\PromoPackage;
use App\Models\Review;
use App\Models\TourPackage;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse|View|RedirectResponse
    {
        if (Auth::guard('admin')->check() || $request->user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $cacheKey = 'tourist_dashboard_' . $request->user()?->id;
        $data = cache()->remember($cacheKey, now()->addMinutes(5), function() use ($request) {
            return $this->touristData($request);
        });

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('dashboard', $data);
    }

    public function home(): View
    {
        $topRatedPackages = TourPackage::active()
            ->bolinao()
            ->where('rating', '>=', 4)
            ->orderBy('rating', 'desc')
            ->limit(3)
            ->get();

        $customerReviews = Review::with(['user', 'tourPackage'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $famousTouristSpots = FamousTouristSpot::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->limit(6)
            ->get();

        $promoPackages = PromoPackage::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->limit(3)
            ->get();

        return view('welcome', compact('topRatedPackages', 'customerReviews', 'famousTouristSpots', 'promoPackages'));
    }

    public function admin(Request $request): JsonResponse|View
    {
        $cacheKey = 'admin_dashboard_' . ($request->user()?->id ?? 'guest');
        $data = cache()->remember($cacheKey, now()->addMinutes(5), function() use ($request) {
            return $this->buildDashboardData($request);
        });

        $data = array_merge([
            'topCategories' => $data['topCategories'] ?? collect(),
            'categoryLabels' => $data['categoryLabels'] ?? TourPackage::categoryLabels(),
        ], $data);

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('admin.dashboard', $data);
    }

    public function famousTouristSpots(Request $request): View
    {
        $spots = FamousTouristSpot::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();

        return view('famous-tourist-spots', compact('spots'));
    }

    public function showFamousTouristSpot($id): View
    {
        $spot = FamousTouristSpot::findOrFail($id);

        return view('famous-tourist-spot-details', compact('spot'));
    }

    public function promoPackages(Request $request): View
    {
        $promoPackages = PromoPackage::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest()
            ->get();

        return view('promo-packages', compact('promoPackages'));
    }

    public function showPromoPackage($id): View
    {
        $promoPackage = PromoPackage::findOrFail($id);

        return view('promo-package-details', compact('promoPackage'));
    }

    public function packages(Request $request): JsonResponse|View|RedirectResponse
    {
        $categoryMap = [
            'natural' => ['label' => 'Natural Attractions', 'keywords' => ['beach','waterfall','falls','cave','river','rock','island','lagoon','spring','shore']],
            'cultural' => ['label' => 'Cultural & Historical Sites', 'keywords' => ['church','heritage','museum','lighthouse','parish','histor','monument','temple']],
            'recreational' => ['label' => 'Recreational & Adventure Spots', 'keywords' => ['island','diving','snorkel','camp','hike','hiking','island hopping','adventure','tour']],
            'accommodation' => ['label' => 'Accommodation & Hospitality', 'keywords' => ['resort','hotel','inn','homestay','transient','guesthouse','lodg','villa']],
            'events' => ['label' => 'Events & Festivals', 'keywords' => ['festival','event','parade','competition','celebration']],
            'ecotourism' => ['label' => 'Ecotourism & Conservation Areas', 'keywords' => ['mangrove','park','reserve','ecolodge','protected','sanctuary']],
        ];

        $selectedDuration = $request->input('duration', 'all');

        if (! in_array($selectedDuration, ['all', '1', '2_4'], true)) {
            $selectedDuration = 'all';
        }

        if ($request->boolean('dur_1') && ! $request->boolean('dur_all')) {
            $selectedDuration = '1';
        } elseif ($request->boolean('dur_2') && ! $request->boolean('dur_all')) {
            $selectedDuration = '2_4';
        }

        $capacity = $request->filled('capacity') && $request->integer('capacity') > 0
            ? $request->integer('capacity')
            : null;

        $selectedPromo = null;
        if ($request->filled('promo')) {
            $promo = PromoPackage::find($request->input('promo'));
            if ($promo?->isActive()) {
                $selectedPromo = $promo;
                $minCapacity = $promo->minGuestCapacity();
                if ($minCapacity && (! $capacity || $capacity < $minCapacity)) {
                    $capacity = $minCapacity;
                }
            }
        }

        $packages = TourPackage::active()
            ->bolinao()
            ->when($request->search, fn($q) => $q->where(function($sub) use ($request) {
                $sub->where('name', 'like', "%{$request->search}%")
                    ->orWhere('location', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            )
            ->when($request->destination, fn($q) => $q->where('destination_id', $request->destination))
            ->when($request->category && array_key_exists($request->category, $categoryMap), function($q) use ($request, $categoryMap) {
                $q->where(function($sub) use ($request, $categoryMap) {
                    $sub->where('category', $request->category);
                    $keywords = $categoryMap[$request->category]['keywords'];
                    $sub->orWhere(function($s2) use ($keywords) {
                        foreach ($keywords as $k) {
                            $s2->orWhere('name', 'like', "%{$k}%")
                               ->orWhere('description', 'like', "%{$k}%")
                               ->orWhere('location', 'like', "%{$k}%");
                        }
                    })->orWhereHas('destination', function($d) use ($keywords) {
                        foreach ($keywords as $k) {
                            $d->orWhere('name', 'like', "%{$k}%");
                        }
                    });
                });
            })
            ->when($selectedDuration !== 'all', function($q) use ($selectedDuration) {
                if ($selectedDuration === '1') {
                    $q->where('duration_days', 1);
                } elseif ($selectedDuration === '2_4') {
                    $q->whereBetween('duration_days', [2, 4]);
                }
            })
            ->when($capacity, fn($q) => $q->where('max_guests', '>=', $capacity))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->orderBy('updated_at', 'desc')
            ->paginate(18)
            ->withQueryString();

        $destinations = Destination::orderBy('name')->get();

        $data = compact('packages', 'destinations', 'categoryMap', 'selectedDuration', 'capacity', 'selectedPromo');

        if ($request->user()) {
            $data = array_merge($this->touristData($request), $data);
        }

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('tourist.packages.index', $data);
    }

    public function storeBooking(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()?->isGuest()) {
            $message = 'Guest accounts can only browse tours. Please register or sign in to book.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $message);
        }

        $validated = $request->validate([
            'tour_package_id' => ['required', 'exists:tour_packages,id'],
            'promo_package_id' => ['nullable', 'exists:promo_packages,id'],
            'tour_start_date' => ['required', 'date', 'after_or_equal:today'],
            'tour_end_date' => ['required', 'date', 'after:tour_start_date'],
            'num_adults' => ['required', 'integer', 'min:0'],
            'num_children' => ['required', 'integer', 'min:0'],
            'num_seniors' => ['required', 'integer', 'min:0'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:50'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'tourist_guide' => ['nullable', 'boolean'],
            'services' => ['nullable', 'array'],
            'services.*' => ['in:airport_transfer,travel_insurance,meal_plan'],
        ]);

        $package = TourPackage::whereKey($validated['tour_package_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $checkIn = Carbon::parse($validated['tour_start_date']);
        $checkOut = Carbon::parse($validated['tour_end_date']);
        $expectedCheckOut = $checkIn->copy()->addDays($package->duration_days);

        if ((int) abs($checkIn->diffInDays($checkOut, false)) !== $package->duration_days) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['tour_end_date' => "Tour end date must be exactly {$package->duration_days} day(s) after tour start for this package. Please select {$expectedCheckOut->format('Y-m-d')}."]);
        }

        $totalGuests = $validated['num_adults'] + $validated['num_children'] + $validated['num_seniors'];

        if ($totalGuests < 1 || $totalGuests > $package->max_guests) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Please select between 1 and {$package->max_guests} guests.");
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

        $touristGuideFee = !empty($validated['tourist_guide']) ? 1200 : 0;

        // Apply promo package discount if provided
        $discountAmount = 0;
        $discountCode = null;
        $promoPackageId = null;

        if (!empty($validated['promo_package_id'])) {
            $promoPackage = PromoPackage::find($validated['promo_package_id']);
            if ($promoPackage && $promoPackage->isActive()) {
                $minStartDays = $promoPackage->minStartDays();
                if ($minStartDays > 0 && $checkIn->lt(now()->addDays($minStartDays))) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['tour_start_date' => "This promo requires tour start at least {$minStartDays} days from today."]);
                }

                $basePrice = $package->price * $totalGuests;
                $discountAmount = $basePrice * ($promoPackage->discount_percentage / 100);
                $discountCode = $promoPackage->name;
                $promoPackageId = $promoPackage->id;
            }
        }

        $bookingService = new BookingService();
        $booking = $bookingService->createBooking([
            'user_id' => $request->user()->id,
            'tour_package_id' => $package->id,
            'promo_package_id' => $promoPackageId,
            'tour_date' => $validated['tour_start_date'],
            'tour_start_date' => $validated['tour_start_date'],
            'tour_end_date' => $validated['tour_end_date'],
            'num_guests' => $totalGuests,
            'num_adults' => $validated['num_adults'],
            'num_children' => $validated['num_children'],
            'num_seniors' => $validated['num_seniors'],
            'base_price' => $package->price * $totalGuests,
            'additional_fees' => $serviceTotal,
            'tourist_guide' => !empty($validated['tourist_guide']),
            'tourist_guide_fee' => $touristGuideFee,
            'discount_amount' => $discountAmount,
            'discount_code' => $discountCode,
            'services' => collect($serviceItems),
            'guest_details' => collect([
                'contact_name' => $validated['guest_name'],
                'contact_email' => $validated['guest_email'],
                'contact_phone' => $validated['guest_phone'],
            ]),
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tour booking request submitted successfully.',
                'data' => $booking->load(['package', 'payment']),
            ], 201);
        }

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'Your booking request has been submitted. We will confirm availability shortly.');
    }

    public function adminBookings(Request $request): JsonResponse|View
    {
        $query = Booking::with(['user', 'package', 'payment', 'approver'])
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10)->appends($request->query());

        if ($request->expectsJson()) {
            return response()->json(['data' => $bookings]);
        }

        return view('admin.bookings', ['bookings' => $bookings]);
    }

    public function adminPackages(Request $request): JsonResponse|View
    {
        $activePackages = TourPackage::where('status', 'active')->count();
        $inactivePackages = TourPackage::where('status', '!=', 'active')->count();

        $query = TourPackage::latest()
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('location', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->category, function ($query) use ($request) {
                $query->where('category', $request->category);
            });

        $packages = $query->paginate(5)->withQueryString();
        $categories = TourPackage::categoryLabels();

        $data = [
            'stats' => [
                'active' => $activePackages,
                'inactive' => $inactivePackages,
                'total' => $activePackages + $inactivePackages,
            ],
            'packages' => $packages,
            'categories' => $categories,
        ];

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('admin.packages-stats', $data);
    }

    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,declined'],
        ]);

        $booking->update([
            'status' => $validated['status'],
            'approved_by' => Auth::guard('admin')->id() ?? $request->user()?->id,
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking status updated successfully.');
    }

    public function approveCancellation(Request $request, Booking $booking): RedirectResponse
    {
        if (!Auth::guard('admin')->check() && $request->user()?->role !== 'admin') {
            return back()->with('error', 'Unauthorized');
        }

        if ($booking->status !== 'cancellation_pending') {
            return back()->with('error', 'This booking is not pending cancellation.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'approved_by' => Auth::guard('admin')->id() ?? $request->user()?->id,
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Cancellation approved successfully.');
    }

    public function rejectCancellation(Request $request, Booking $booking): RedirectResponse
    {
        if (!Auth::guard('admin')->check() && $request->user()?->role !== 'admin') {
            return back()->with('error', 'Unauthorized');
        }

        if ($booking->status !== 'cancellation_pending') {
            return back()->with('error', 'This booking is not pending cancellation.');
        }

        $booking->update([
            'status' => 'pending',
            'cancellation_reason' => null,
        ]);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Cancellation rejected. Booking restored to pending status.');
    }

    private function touristData(Request $request): array
    {
        $user = $request->user();
        
        // Optimize: Single query with aggregation instead of multiple count queries
        $bookingStats = Booking::where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            ")
            ->first();
        
        $bookings = Booking::with(['user', 'package', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Optimize: Single query for payment stats
        $paymentStats = Payment::whereIn('booking_id', Booking::where('user_id', $user->id)->select('id'))
            ->selectRaw("
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as revenue
            ")
            ->first();

        // Get top-rated packages
        $topRatedPackages = TourPackage::active()
            ->bolinao()
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        // Get recent reviews from the user
        $userReviews = Review::where('user_id', $user->id)
            ->with(['tourPackage'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent reviews from all users (for recommendations)
        $communityReviews = Review::with(['user', 'tourPackage'])
            ->where('rating', '>=', 4)
            ->latest()
            ->limit(6)
            ->get();

        // Get booking breakdown by status
        $bookingsByStatus = [
            'approved' => $bookingStats->approved ?? 0,
            'pending' => $bookingStats->pending ?? 0,
            'cancelled' => $bookingStats->cancelled ?? 0,
        ];

        // Get most booked destinations
        $topDestinations = TourPackage::active()
            ->bolinao()
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'stats' => [
                'packages' => TourPackage::active()->bolinao()->count(),
                'bookings' => $bookingStats->total ?? 0,
                'pending_bookings' => $bookingStats->pending ?? 0,
                'paid_payments' => $paymentStats->paid_count ?? 0,
                'revenue' => $paymentStats->revenue ?? 0,
                'bookingsByStatus' => $bookingsByStatus,
            ],
            'availablePackages' => TourPackage::query()
                ->active()
                ->bolinao()
                ->latest()
                ->get(),
            'topRatedPackages' => $topRatedPackages,
            'recentBookings' => $bookings,
            'userReviews' => $userReviews,
            'communityReviews' => $communityReviews,
            'topDestinations' => $topDestinations,
            'user' => $user,
        ];
    }

    private function buildDashboardData(Request $request): array
    {
        $user = Auth::guard('admin')->user() ?? $request->user();

        // Optimize: Single query for booking stats aggregation
        // Make metrics mutually exclusive based on booking lifecycle
        $bookingStats = Booking::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN tour_started_at IS NOT NULL AND tour_ended_at IS NULL THEN 1 ELSE 0 END) as checked_in,
            SUM(CASE WHEN tour_ended_at IS NOT NULL THEN 1 ELSE 0 END) as checked_out
        ")
        ->first();

        // Optimize: Single query for payment stats
        // Count paid bookings that are not already in tour progress
        $paymentStats = Payment::selectRaw("
            SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as revenue
        ")
        ->first();

        // Get paid bookings count excluding those with tour progress
        $paidBookingsWithoutProgress = Booking::whereHas('payment', function($q) {
            $q->where('status', 'paid');
        })
        ->whereNull('tour_started_at')
        ->whereNull('tour_ended_at')
        ->count();

        // Get recent bookings with details
        $recentBookings = Booking::with(['user', 'package', 'payment'])
            ->latest()
            ->limit(10)
            ->get();

        // Get top packages by bookings and live review average
        $topPackages = TourPackage::withCount(['bookings', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('bookings_count')
            ->orderByDesc('reviews_avg_rating')
            ->limit(5)
            ->get();

        // Get famous tourist spots
        $famousTouristSpots = FamousTouristSpot::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->limit(5)
            ->get();

        // Get monthly booking data (last 6 months)
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $monthlyBookings = Booking::selectRaw('EXTRACT(MONTH FROM created_at)::int as month, COUNT(*) as count, SUM(total_price) as revenue')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');
        } else {
            $monthlyBookings = Booking::selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, COUNT(*) as count, SUM(total_price) as revenue")
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');
        }

        // Get booking status breakdown
        $bookingsByStatus = [
            'pending' => $bookingStats->pending ?? 0,
            'approved' => $bookingStats->approved ?? 0,
            'cancelled' => $bookingStats->cancelled ?? 0,
        ];

        // Get top categories by total bookings
        $topCategories = Booking::join('tour_packages', 'bookings.tour_package_id', '=', 'tour_packages.id')
            ->select('tour_packages.category', DB::raw('COUNT(bookings.id) as total_bookings'))
            ->groupBy('tour_packages.category')
            ->orderByDesc('total_bookings')
            ->limit(3)
            ->get();

        $categoryLabels = TourPackage::categoryLabels();

        // Get payment breakdown
        $paymentsByStatus = [
            'paid' => $paymentStats->paid_count ?? 0,
            'pending' => $paymentStats->pending_count ?? 0,
            'failed' => $paymentStats->failed_count ?? 0,
        ];

        // Get recent reviews
        $recentReviews = Review::with(['user', 'tourPackage'])
            ->latest()
            ->limit(8)
            ->get();

        // Calculate customer satisfaction
        $reviewStats = Review::selectRaw('
            AVG(rating) as avg_rating,
            COUNT(*) as total_reviews
        ')
        ->first();

        // Get active users count
        $activeUsers = Booking::distinct('user_id')->count('user_id');

        // Get upcoming check-ins (tours starting within next 7 days with confirmed status)
        $upcomingCheckIns = Booking::with(['user', 'package'])
            ->where('status', 'confirmed')
            ->whereNull('tour_started_at')  // Not yet checked in
            ->whereBetween('tour_start_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->orderBy('tour_start_date', 'asc')
            ->get();

        return [
            'stats' => [
                'packages' => TourPackage::count(),
                'bookings' => $bookingStats->total ?? 0,
                'pending_bookings' => $bookingStats->pending ?? 0,
                'checked_in_bookings' => $bookingStats->checked_in ?? 0,
                'checked_out_bookings' => $bookingStats->checked_out ?? 0,
                'paid_payments' => $paidBookingsWithoutProgress,
                'revenue' => $paymentStats->revenue ?? 0,
                'bookingsByStatus' => $bookingsByStatus,
                'paymentsByStatus' => $paymentsByStatus,
                'avgRating' => round($reviewStats->avg_rating ?? 0, 2),
                'totalReviews' => $reviewStats->total_reviews ?? 0,
                'activeUsers' => $activeUsers,
            ],
            'recentBookings' => $recentBookings,
            'topPackages' => $topPackages,
            'topCategories' => $topCategories,
            'categoryLabels' => $categoryLabels,
            'famousTouristSpots' => $famousTouristSpots,
            'monthlyBookings' => $monthlyBookings,
            'recentReviews' => $recentReviews,
            'upcomingCheckIns' => $upcomingCheckIns,
        ];
    }

    private function bookingNumber(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_number', $code)->exists());

        return $code;
    }

    // Enhanced Booking Management Methods

    public function showBooking(Booking $booking): View
    {
        $this->authorizeBookingAccess($booking);

        return view('bookings.show', [
            'booking' => $booking->load(['user', 'package', 'payment', 'approver']),
            'remainingDays' => $booking->remaining_days,
        ]);
    }

    public function cancelBooking(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
            'confirm' => ['required', 'boolean'],
        ]);

        if (!$validated['confirm']) {
            return back()->with('error', 'Cancellation not confirmed.');
        }

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $bookingService = new \App\Services\BookingService();
        $bookingService->cancelBooking(
            $booking,
            $validated['reason'] ?? 'User requested cancellation'
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking cancelled successfully.']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Booking cancelled successfully. Refund will be processed.');
    }

    public function addNote(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'in:internal,admin'],
        ]);

        $bookingService = new \App\Services\BookingService();

        if ($validated['type'] === 'internal') {
            $bookingService->addInternalNote($booking, $validated['note']);
        } else {
            $userName = $request->user()?->name ?? 'System';
            $bookingService->addAdminNote($booking, $validated['note'], $userName);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Note added successfully.']);
        }

        return back()->with('success', 'Note added successfully.');
    }

    public function updateGuests(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validate([
            'guests.*.name' => ['required', 'string', 'max:100'],
            'guests.*.email' => ['nullable', 'email'],
            'guests.*.phone' => ['nullable', 'string'],
            'guests.*.age' => ['nullable', 'integer', 'min:1', 'max:150'],
        ]);

        $bookingService = new \App\Services\BookingService();
        $bookingService->updateGuestDetails($booking, $validated['guests'] ?? []);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Guest details updated.']);
        }

        return back()->with('success', 'Guest details updated successfully.');
    }

    public function applyDiscount(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        if (!Auth::guard('admin')->check() && $request->user()?->role !== 'admin') {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'discount_code' => ['required', 'string'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $bookingService = new \App\Services\BookingService();
        $bookingService->applyDiscount(
            $booking,
            $validated['discount_amount'],
            $validated['discount_code']
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Discount applied.', 'booking' => $booking]);
        }

        return back()->with('success', 'Discount applied successfully.');
    }

    public function confirmBooking(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        if (!Auth::guard('admin')->check() && $request->user()?->role !== 'admin') {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $bookingService = new \App\Services\BookingService();
        $bookingService->confirmBooking($booking, $validated['admin_notes'] ?? '');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking confirmed.']);
        }

        return back()->with('success', 'Booking confirmed successfully.');
    }

    public function exportBooking(Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        // Generate PDF or download as file
        $html = view('bookings.pdf', ['booking' => $booking->load(['user', 'package', 'payment'])])->render();

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"Booking_{$booking->booking_number}.pdf\"");
    }

    private function authorizeBookingAccess(Booking $booking): void
    {
        $user = auth()->user();
        $isAdmin = Auth::guard('admin')->check() || $user?->role === 'admin';
        $isOwner = $user?->id === $booking->user_id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'Unauthorized access to booking.');
        }
    }
}
