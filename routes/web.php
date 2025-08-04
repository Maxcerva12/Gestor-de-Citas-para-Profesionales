<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePreviewController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-calendar/auth', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.auth');
Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'handleGoogleCallback'])->name('google.callback');


Route::middleware(['auth'])->group(function () {
    // Ruta para vista previa de configuración de facturas
    Route::get('/invoice-preview', [InvoicePreviewController::class, 'generatePreview'])
        ->name('invoice.preview');
});

// Rutas de facturas (sin middleware de autenticación para pruebas)
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'viewPdf'])
    ->name('invoices.pdf');
Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'downloadPdf'])
    ->name('invoices.download');