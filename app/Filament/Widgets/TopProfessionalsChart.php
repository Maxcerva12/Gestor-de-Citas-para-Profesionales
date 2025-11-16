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

    /**
     * Determinar unidad (K, M, B) basada en el valor máximo
     */
    private function determineUnit(array $values): string
    {
        if (empty($values)) return '';
        
        $maxValue = max($values);
        
        if ($maxValue >= 1000000000) {
            return 'B';
        } elseif ($maxValue >= 1000000) {
            return 'M';
        } elseif ($maxValue >= 1000) {
            return 'K';
        }
        return '';
    }
    
    /**
     * Formatear valores para display manteniendo como números
     */
    private function formatForDisplay(array $values, string $unit): array
    {
        return array_map(function($value) use ($unit) {
            switch ($unit) {
                case 'B':
                    return (float) round($value / 1000000000, 1);
                case 'M':
                    return (float) round($value / 1000000, 1);
                case 'K':
                    return (float) round($value / 1000, 1);
                default:
                    return (float) $value;
            }
        }, $values);
    }

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
        $rawData = array_column($rankingData, 'value');
        
        // Determinar unidad y formatear valores
        $unit = $this->determineUnit($rawData);
        $formattedData = $this->formatForDisplay($rawData, $unit);

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
                    'name' => 'Ingresos' . ($unit ? ' (' . $unit . ')' : ''),
                    'data' => $formattedData,
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
                'title' => [
                    'text' => 'Ingresos' . ($unit ? ' (' . $unit . ')' : ' (COP)'),
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
                'shared' => true,
                'intersect' => false,
            ],
        ];
    }
}