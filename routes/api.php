<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TourPackageController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [TourPackageController::class, 'index']);
Route::get('/packages/{package}', [TourPackageController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/payments', [PaymentController::class, 'index']);

    Route::post('/packages', [TourPackageController::class, 'store']);
    Route::post('/bookings', [BookingController::class, 'store']);

    Route::put('/packages/{package}', [TourPackageController::class, 'update']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);

    Route::delete('/packages/{package}', [TourPackageController::class, 'destroy']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::apiResource('bookings', BookingController::class)->only(['show']);
    Route::apiResource('payments', PaymentController::class)->except(['index', 'destroy']);

    Route::apiResource('bookings', BookingController::class);
    
    // Enhanced booking management endpoints
    Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/services', [BookingController::class, 'addServices'])->name('bookings.services');
    Route::post('bookings/{booking}/discount', [BookingController::class, 'applyDiscount'])->name('bookings.discount');
    Route::post('bookings/{booking}/notes', [BookingController::class, 'addNote'])->name('bookings.notes');
    Route::post('bookings/{booking}/guests', [BookingController::class, 'updateGuests'])->name('bookings.guests');
    Route::post('bookings/{booking}/reminder-sent', [BookingController::class, 'markReminderSent'])->name('bookings.reminder-sent');
    Route::post('bookings/{booking}/payment-plan', [BookingController::class, 'setupPaymentPlan'])->name('bookings.payment-plan');
    Route::get('bookings/reminders/due', [BookingController::class, 'getDueReminders'])->name('bookings.reminders.due');
    
    Route::apiResource('payments', PaymentController::class)->only([
        'index',
        'store',
        'show',
    ]);
});
