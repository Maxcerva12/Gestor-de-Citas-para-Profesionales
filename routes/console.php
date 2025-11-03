<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar la tarea para marcar horarios expirados cada 30 minutos
Schedule::command('schedules:mark-expired')->everyThirtyMinutes();

// Programar la tarea para cancelar citas vencidas cada hora
Schedule::command('appointments:cancel-expired')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
