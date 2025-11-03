<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopProfessionalsChart extends ApexChartWidget
{
    use HasWidgetShield;

    protected static ?string $chartId = 'topProfessionalsChart';
    protected static ?int $height = 400;
    protected static ?int $sort = 4;

    protected function getHeading(): string
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        return $canViewAllRevenue
            ? 'Top Profesionales por Ingresos'
            : 'Mis Servicios Más Rentables';
    }

    protected function getSubHeading(): ?string
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        return $canViewAllRevenue
            ? 'Ranking de profesionales con mayores ingresos'
            : 'Top 10 de mis servicios ordenados por ingresos';
    }

    protected function getOptions(): array
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        $service = app(DashboardDataService::class);
        $dashboardData = $service->getDashboardData($currentUser, $canViewAllRevenue);
        $rankingData = $dashboardData['top_ranking'];

        $labels = array_column($rankingData, 'label');
        $data = array_column($rankingData, 'value');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
                'toolbar' => [
                    'show' => true,
                ],
            ],
            'series' => [
                [
                    'name' => 'Ingresos',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                    'rotate' => -45,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => [$canViewAllRevenue ? '#3B82F6' : '#10B981'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'grid' => [
                'show' => true,
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => null,
                ],
            ],
        ];
    }
}