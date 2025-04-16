<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-calendar/auth', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');
