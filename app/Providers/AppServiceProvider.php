<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Filament\Widgets\ScheduleCalendarWidget;
use App\Models\Appointment; // Añadir esta importación
use App\Observers\AppointmentObserver; // Añadir esta importación
use App\Models\Client; // Añadir esta importación
use App\Observers\ClientOdontogramObserver; // Añadir esta importación
use App\Models\InvoiceItem; // Añadir esta importación
use App\Observers\InvoiceItemObserver; // Añadir esta importación
use App\Models\Invoice; // Añadir para Invoice
use App\Observers\InvoiceObserver; // Añadir para Invoice
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar DashboardDataService como singleton para reutilizar instancia
        $this->app->singleton(\App\Services\DashboardDataService::class);
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
        Client::observe(ClientOdontogramObserver::class);
        InvoiceItem::observe(InvoiceItemObserver::class);
        Invoice::observe(InvoiceObserver::class); // Registrar el nuevo observer
    }

    /**
     * Los observadores que se registrarán para los modelos.
     */
    protected $observers = [
        Appointment::class => [AppointmentObserver::class],
        Client::class => [ClientOdontogramObserver::class],
        Invoice::class => [InvoiceObserver::class], // Agregar Invoice
    ];
}
