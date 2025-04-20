<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Filament\Widgets\ScheduleCalendarWidget;
use App\Models\Appointment; // Añadir esta importación
use App\Observers\AppointmentObserver; // Añadir esta importación
use App\Models\Price; // Añadir esta importación
use App\Observers\PriceObserver; // Añadir esta importación
// use App\Models\Cashier\User;
use Laravel\Cashier\Cashier;
use App\Models\User;

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

        // Registra los observadores
        Appointment::observe(AppointmentObserver::class);
        Price::observe(PriceObserver::class);

        // Configura el modelo de usuario para Laravel Cashier
        Cashier::useCustomerModel(User::class);
        // Configura los impuestos para Laravel Cashier
        // Puedes personalizar la configuración de impuestos según tus necesidades
        // Cashier::calculateTaxes();
    }

    /**
     * Los observadores que se registrarán para los modelos.
     */
    protected $observers = [
        Appointment::class => [AppointmentObserver::class],
        Price::class => [PriceObserver::class],
    ];
}
