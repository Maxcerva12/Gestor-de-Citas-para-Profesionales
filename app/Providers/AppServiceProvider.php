<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Filament\Widgets\ScheduleCalendarWidget;
use App\Models\Appointment; // Añadir esta importación
use App\Observers\AppointmentObserver; // Añadir esta importación

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra manualmente el widget en Livewire
        Livewire::component('schedule-calendar-widget', ScheduleCalendarWidget::class);

        // Registra el observador para el modelo Appointment
        Appointment::observe(AppointmentObserver::class);
    }

    /**
     * Los observadores que se registrarán para los modelos.
     */
    protected $observers = [
        Appointment::class => [AppointmentObserver::class],
    ];
}
