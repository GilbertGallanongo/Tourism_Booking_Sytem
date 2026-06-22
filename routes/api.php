<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FamousTouristSpotController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PromoPackageController;
use App\Http\Controllers\Api\TourPackageController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [TourPackageController::class, 'index']);
Route::get('/packages/{package}', [TourPackageController::class, 'show']);
Route::get('/promo-packages', [PromoPackageController::class, 'index']);
Route::get('/promo-packages/{promoPackage}', [PromoPackageController::class, 'show']);
Route::get('/tourist-spots', [FamousTouristSpotController::class, 'index']);
Route::get('/tourist-spots/{famousTouristSpot}', [FamousTouristSpotController::class, 'show']);

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
    Route::delete('/packages/delete-all', [TourPackageController::class, 'destroyAll'])->name('packages.destroy-all');
    Route::delete('/packages/{package}', [TourPackageController::class, 'destroy'])->name('packages.destroy');

    Route::post('/promo-packages', [PromoPackageController::class, 'store'])->name('promo-packages.store');
    Route::post('/promo-packages/{promoPackage}/image', [PromoPackageController::class, 'uploadImage'])->name('promo-packages.image');
    Route::put('/promo-packages/{promoPackage}', [PromoPackageController::class, 'update'])->name('promo-packages.update');
    Route::delete('/promo-packages/delete-all', [PromoPackageController::class, 'destroyAll'])->name('promo-packages.destroy-all');
    Route::delete('/promo-packages/{promoPackage}', [PromoPackageController::class, 'destroy'])->name('promo-packages.destroy');

    Route::post('/tourist-spots', [FamousTouristSpotController::class, 'store'])->name('tourist-spots.store');
    Route::post('/tourist-spots/{famousTouristSpot}/image', [FamousTouristSpotController::class, 'uploadImage'])->name('tourist-spots.image');
    Route::put('/tourist-spots/{famousTouristSpot}', [FamousTouristSpotController::class, 'update'])->name('tourist-spots.update');
    Route::delete('/tourist-spots/delete-all', [FamousTouristSpotController::class, 'destroyAll'])->name('tourist-spots.destroy-all');
    Route::delete('/tourist-spots/{famousTouristSpot}', [FamousTouristSpotController::class, 'destroy'])->name('tourist-spots.destroy');

    Route::get('/bookings/reminders/due', [BookingController::class, 'getDueReminders'])->name('bookings.reminders.due');
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/services', [BookingController::class, 'addServices'])->name('bookings.services');
    Route::post('/bookings/{booking}/discount', [BookingController::class, 'applyDiscount'])->name('bookings.discount');
    Route::post('/bookings/{booking}/notes', [BookingController::class, 'addNote'])->name('bookings.notes');
    Route::post('/bookings/{booking}/guests', [BookingController::class, 'updateGuests'])->name('bookings.guests');
    Route::post('/bookings/{booking}/reminder-sent', [BookingController::class, 'markReminderSent'])->name('bookings.reminder-sent');
    Route::post('/bookings/{booking}/payment-plan', [BookingController::class, 'setupPaymentPlan'])->name('bookings.payment-plan');
    Route::delete('/bookings/delete-all', [BookingController::class, 'destroyAll'])->name('bookings.destroy-all');
    Route::apiResource('bookings', BookingController::class);

    Route::post('/payments/{payment}/proof', [PaymentController::class, 'uploadProof'])->name('payments.proof');
    Route::delete('/payments/delete-all', [PaymentController::class, 'destroyAll'])->name('payments.destroy-all');
    Route::apiResource('payments', PaymentController::class);
});
