<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TourPackageController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [TourPackageController::class, 'index']);
Route::get('/packages/{package}', [TourPackageController::class, 'show']);

Route::post('/login', [TokenController::class, 'loginTourist'])->name('api.login');
Route::post('/admin/login', [TokenController::class, 'loginAdmin'])->name('api.admin.login');

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [TokenController::class, 'logout'])->name('tokens.logout');
    Route::post('/tokens', [TokenController::class, 'createToken'])->name('tokens.create');
    Route::get('/tokens', [TokenController::class, 'listTokens'])->name('tokens.list');
    Route::delete('/tokens/{tokenId}', [TokenController::class, 'revokeToken'])->name('tokens.revoke');
    Route::post('/tokens/revoke-all', [TokenController::class, 'revokeAllTokens'])->name('tokens.revoke-all');

    Route::post('/packages', [TourPackageController::class, 'store'])->name('packages.store');
    Route::post('/packages/{package}/image', [TourPackageController::class, 'uploadImage'])->name('packages.image');
    Route::put('/packages/{package}', [TourPackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [TourPackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('/bookings/reminders/due', [BookingController::class, 'getDueReminders'])->name('bookings.reminders.due');
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/services', [BookingController::class, 'addServices'])->name('bookings.services');
    Route::post('/bookings/{booking}/discount', [BookingController::class, 'applyDiscount'])->name('bookings.discount');
    Route::post('/bookings/{booking}/notes', [BookingController::class, 'addNote'])->name('bookings.notes');
    Route::post('/bookings/{booking}/guests', [BookingController::class, 'updateGuests'])->name('bookings.guests');
    Route::post('/bookings/{booking}/reminder-sent', [BookingController::class, 'markReminderSent'])->name('bookings.reminder-sent');
    Route::post('/bookings/{booking}/payment-plan', [BookingController::class, 'setupPaymentPlan'])->name('bookings.payment-plan');
    Route::apiResource('bookings', BookingController::class);

    Route::post('/payments/{payment}/proof', [PaymentController::class, 'uploadProof'])->name('payments.proof');
    Route::apiResource('payments', PaymentController::class);
});
