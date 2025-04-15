<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ScheduleCalendarWidget;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Calendar extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.calendar';

    protected static ?string $navigationLabel = 'Calendario';

    protected function getHeaderWidgets(): array
    {
        return [
            ScheduleCalendarWidget::class,
        ];
    }
}
