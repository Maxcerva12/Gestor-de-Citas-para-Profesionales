<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TotalAppointmentsChart extends ApexChartWidget
{
    use HasWidgetShield;
    
    protected static ?string $chartId = 'totalAppointmentsChart';
    protected static ?string $heading = 'Total de Citas Reservadas';
    protected static ?string $subheading = 'Todos los datos';

    protected function getOptions(): array
    {
        $currentUser = Auth::user();
        $canViewAll = $currentUser->hasRole('super_admin');

        $service = app(DashboardDataService::class);
        $dashboardData = $service->getDashboardData($currentUser, $canViewAll);
        $appointmentsData = $dashboardData['appointments']['by_date'];

        $dates = array_column($appointmentsData, 'date');
        $totals = array_column($appointmentsData, 'total');

        return [
            'chart' => [
                'type' => 'line',
                'height' => 290,
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
                    ],
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
