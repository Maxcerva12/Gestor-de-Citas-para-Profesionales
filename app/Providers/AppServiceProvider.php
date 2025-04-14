<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Filament\Widgets\ScheduleCalendarWidget;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Registra manualmente el widget en Livewire
        Livewire::component('schedule-calendar-widget', ScheduleCalendarWidget::class);
    }
}
