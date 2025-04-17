<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-calendar/auth', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');


Route::middleware(['auth'])->group(function () {
    Route::get('/payment/{appointment}/checkout', [PaymentController::class, 'checkout'])
        ->name('payment.checkout');
    Route::get('/payment/success/{appointment}', [PaymentController::class, 'success'])
        ->name('payment.success');
    Route::get('/payment/cancel/{appointment}', [PaymentController::class, 'cancel'])
        ->name('payment.cancel');
});