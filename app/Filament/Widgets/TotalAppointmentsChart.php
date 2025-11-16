<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;

class TotalAppointmentsChart extends ApexChartWidget
{
    use HasWidgetShield;
    
    protected static ?string $chartId = 'totalAppointmentsChart';
    
    public ?string $filter = 'last_7_days';
    
    protected function getFilters(): ?array
    {
        return [
            'last_7_days' => 'Últimos 7 días',
            'last_4_weeks' => 'Últimas 4 semanas',
            'last_6_months' => 'Últimos 6 meses',
            'last_5_years' => 'Últimos 5 años',
        ];
    }
    
    protected function getHeading(): string
    {
        return match($this->filter) {
            'last_7_days' => 'Citas Reservadas - Últimos 7 días',
            'last_4_weeks' => 'Citas Reservadas - Últimas 4 semanas',
            'last_6_months' => 'Citas Reservadas - Últimos 6 meses',
            'last_5_years' => 'Citas Reservadas - Últimos 5 años',
            default => 'Total de Citas Reservadas',
        };
    }

    protected function getOptions(): array
    {
        $currentUser = Auth::user();
        $canViewAll = $currentUser->hasRole('super_admin');

        $service = app(DashboardDataService::class);
        
        // Obtener datos según el filtro seleccionado
        if ($this->filter === 'last_4_weeks') {
            $appointmentsData = $service->getWeeklyAppointmentsData($currentUser, $canViewAll, $this->filter);
        } elseif ($this->filter === 'last_6_months') {
            $appointmentsData = $service->getMonthlyAppointmentsData($currentUser, $canViewAll, $this->filter);
        } elseif ($this->filter === 'last_5_years') {
            $appointmentsData = $service->getYearlyAppointmentsData($currentUser, $canViewAll, $this->filter);
        } else {
            $appointmentsData = $service->getFilteredAppointmentsData($currentUser, $canViewAll, $this->filter);
        }

        $dates = array_column($appointmentsData, 'date');
        $totals = array_column($appointmentsData, 'total');

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
                'width' => '100%',
                
            ],
            'series' => [
                [
                    'name' => 'Citas',
                    'data' => $totals,
                ],
            ],
            'xaxis' => [
                'categories' => $dates,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '12px',
                    ],
                    'rotate' => -45,
                    'maxHeight' => 60,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#1E90FF'],
        ];
    }
}
