<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AppointmentsByStatusChart extends ApexChartWidget
{
    use HasWidgetShield;
    
    protected static ?string $chartId = 'appointmentsByStatusChart';
    protected static ?string $heading = 'Citas por Estado';
    protected static ?string $subheading = 'Distribución actual de citas';

    protected function getOptions(): array
    {
        $currentUser = Auth::user();
        $canViewAll = $currentUser->hasRole('super_admin');

        $service = app(DashboardDataService::class);
        $dashboardData = $service->getDashboardData($currentUser, $canViewAll);
        $statusCounts = $dashboardData['appointments']['by_status'];

        $labels = [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'canceled' => 'Cancelada',
            'completed' => 'Completada',
        ];

        $data = [];
        $displayLabels = [];
        foreach ($labels as $key => $label) {
            $data[] = $statusCounts[$key] ?? 0;
            $displayLabels[] = $label;
        }

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $data,
            'labels' => $displayLabels,
            'colors' => ['#F59E0B', '#10B981', '#EF4444', '#3B82F6'],
            'legend' => [
                'fontFamily' => 'inherit',
            ],
        ];
    }
}
