<?php

use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\FamousTouristSpotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageViewerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Tourist\BookingController;
use App\Http\Controllers\Tourist\PackageController as TouristPackageController;
use App\Http\Controllers\Tourist\ReservationController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'home'])->name('home');

// Health check endpoint for Docker/Railway
Route::get('/health', function () {
    return response()->json(['status' => 'healthy'], 200);
})->name('health');

Route::get('/uploads/{path}', function (string $path) {
    $path = ltrim($path, '/');

    abort_if($path === '' || str_contains($path, '..') || str_contains($path, '\\'), 404);
    abort_unless(Storage::disk('public')->exists($path), 404);

    return response()->file(Storage::disk('public')->path($path), [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('uploads.show');

Route::get('/image-viewer', [ImageViewerController::class, 'show'])
    ->name('images.view');

// Diagnostics endpoint (enabled only when FORCE_APP_DEBUG=true)
use App\Http\Controllers\DiagnosticsController;
Route::get('/_diagnostics', [DiagnosticsController::class, 'status'])->name('diagnostics.status');

Route::middleware('guest:web')->group(function () {
    // Keep a lightweight GET route for legacy '/login' links.
    Route::get('/login', function () { return redirect()->route('home', ['auth' => 'signin']); })->name('login');
    Route::get('/register', function () { return redirect()->route('home', ['auth' => 'register']); });

    Route::post('/login', [AuthController::class, 'loginTourist'])->name('login.store');
    Route::post('/guest-login', [AuthController::class, 'guestLogin'])->name('guest.login');
});

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/packages', [DashboardController::class, 'packages'])
    ->name('packages.index');

Route::get('/famous-tourist-spots', [DashboardController::class, 'famousTouristSpots'])
    ->name('famous-tourist-spots.index');

Route::get('/famous-tourist-spots/{id}', [DashboardController::class, 'showFamousTouristSpot'])
    ->name('famous-tourist-spots.show');

Route::get('/promo-packages', [DashboardController::class, 'promoPackages'])
    ->name('promo-packages.index');

Route::get('/promo-packages/{id}', [DashboardController::class, 'showPromoPackage'])
    ->name('promo-packages.show');

Route::get('/packages/{tourPackage}', [TouristPackageController::class, 'show'])
    ->name('packages.show');

Route::post('/packages/{tourPackage}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])
    ->middleware(['auth', 'not.guest'])
    ->name('reviews.store');

Route::patch('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'update'])
    ->middleware(['auth', 'not.guest'])
    ->name('reviews.update');

Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])
    ->middleware(['auth', 'not.guest'])
    ->name('reviews.destroy');

Route::get('/bookings/{tourPackage}/create', [BookingController::class, 'create'])
    ->middleware(['auth', 'not.guest'])
    ->name('bookings.create');

Route::get('/reservations', [ReservationController::class, 'index'])
    ->middleware('auth')
    ->name('reservations.index');

Route::get('/my-reservations', function () {
    return redirect()->route('reservations.index');
})
    ->middleware('auth');

Route::get('/reservations/{booking}', [ReservationController::class, 'show'])
    ->middleware('auth')
    ->name('reservations.show');

Route::get('/reservations/{booking}/proof', [ReservationController::class, 'proof'])
    ->middleware('auth')
    ->name('reservations.proof');

Route::get('/reservations/{booking}/edit', [ReservationController::class, 'edit'])
    ->middleware(['auth', 'not.guest'])
    ->name('reservations.edit');

Route::patch('/reservations/{booking}', [ReservationController::class, 'update'])
    ->middleware(['auth', 'not.guest'])
    ->name('reservations.update');

Route::delete('/reservations/{booking}', [ReservationController::class, 'cancel'])
    ->middleware('auth')
    ->name('reservations.cancel');

Route::post('/reservations/{booking}/check-in', [ReservationController::class, 'checkIn'])
    ->middleware('auth')
    ->name('reservations.check-in');

Route::post('/reservations/{booking}/check-out', [ReservationController::class, 'checkOut'])
    ->middleware('auth')
    ->name('reservations.check-out');

Route::post('/reservations/{booking}/payment', [ReservationController::class, 'submitPayment'])
    ->middleware(['auth', 'not.guest'])
    ->name('reservations.payment');

Route::post('/bookings', [DashboardController::class, 'storeBooking'])
    ->middleware(['auth', 'not.guest'])
    ->name('bookings.store');

Route::middleware(['auth', 'not.guest'])->group(function () {
    Route::get('/bookings/{booking}', [DashboardController::class, 'showBooking'])
        ->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [DashboardController::class, 'cancelBooking'])
        ->name('bookings.cancel');
    Route::post('/bookings/{booking}/notes', [DashboardController::class, 'addNote'])
        ->name('bookings.add-note');
    Route::post('/bookings/{booking}/guests', [DashboardController::class, 'updateGuests'])
        ->name('bookings.update-guests');
    Route::get('/bookings/{booking}/export', [DashboardController::class, 'exportBooking'])
        ->name('bookings.export');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware([EnsureAdmin::class])
    ->group(function () {
        Route::post('/bookings/{booking}/confirm', [DashboardController::class, 'confirmBooking'])
            ->name('bookings.confirm');
        Route::post('/bookings/{booking}/discount', [DashboardController::class, 'applyDiscount'])
            ->name('bookings.discount');
    });

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->middleware([EnsureAdmin::class])
    ->name('admin.dashboard');

Route::prefix('admin')
    ->name('admin.')
    ->middleware([EnsureAdmin::class])
    ->group(function () {
        Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
        Route::post('/packages/{package}/upload-image', [PackageController::class, 'uploadImage'])->name('packages.upload-image');
        Route::post('/packages/{package}/upload-chunk', [PackageController::class, 'uploadChunk'])->name('packages.upload-chunk');
        Route::post('/packages/{package}/complete-upload', [PackageController::class, 'completeUpload'])->name('packages.complete-upload');
        Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');
        Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

        Route::resource('famous-tourist-spots', FamousTouristSpotController::class)->except(['show']);
        Route::post('/famous-tourist-spots/{famousTouristSpot}/upload-image', [\App\Http\Controllers\Admin\FamousTouristSpotController::class, 'uploadImage'])->name('famous-tourist-spots.upload-image');

        Route::resource('destinations', \App\Http\Controllers\Admin\DestinationController::class)->except(['show']);

        Route::resource('promo-packages', \App\Http\Controllers\Admin\PromoPackageController::class)->except(['show']);
        Route::post('/promo-packages/{promoPackage}/upload-image', [\App\Http\Controllers\Admin\PromoPackageController::class, 'uploadImage'])->name('promo-packages.upload-image');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('/reports/bookings/{format}', [ReportController::class, 'bookings'])
            ->whereIn('format', ['json', 'csv', 'xlsx', 'pdf'])
            ->name('reports.bookings');

        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class)->only(['index', 'edit', 'update']);
        Route::get('/payments/{payment}/proof', [\App\Http\Controllers\Admin\PaymentController::class, 'proof'])->name('payments.proof');

        Route::get('/bookings', [DashboardController::class, 'adminBookings'])->name('bookings.index');
        Route::get('/packages-stats', [DashboardController::class, 'adminPackages'])->name('packages-stats');
        Route::patch('/bookings/{booking}/status', [DashboardController::class, 'updateBookingStatus'])
            ->name('bookings.status');
        Route::post('/bookings/{booking}/approve-cancellation', [DashboardController::class, 'approveCancellation'])
            ->name('bookings.approve-cancellation');
        Route::post('/bookings/{booking}/reject-cancellation', [DashboardController::class, 'rejectCancellation'])
            ->name('bookings.reject-cancellation');
    });
