<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class MonthlyRevenueChart extends ApexChartWidget
{
    use HasWidgetShield;

    protected static ?string $chartId = 'monthlyRevenueChart';
    protected static ?string $heading = 'Evolución Mensual de Ingresos';
    protected static ?string $subheading = 'Últimos 12 meses';

    protected function getOptions(): array
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        $service = app(DashboardDataService::class);
        $dashboardData = $service->getDashboardData($currentUser, $canViewAllRevenue);
        $monthlyData = $dashboardData['monthly_revenue'];

        if ($canViewAllRevenue) {
            return $this->getFoundationMonthlyChart($monthlyData);
        }

        return $this->getProfessionalMonthlyChart($monthlyData);
    }

    private function getFoundationMonthlyChart(array $monthlyData): array
    {
        $monthLabels = array_column($monthlyData, 'label');
        $values = array_column($monthlyData, 'value');

        $nonZeroValues = array_filter($values, fn($v) => $v > 0);
        $average = count($nonZeroValues) > 0 ? array_sum($nonZeroValues) / count($nonZeroValues) : 0;

        $series = [
            [
                'name' => 'Ingresos Totales',
                'data' => $values,
                'color' => '#10B981',
            ]
        ];

        if ($average > 0) {
            $series[] = [
                'name' => 'Promedio',
                'data' => array_fill(0, count($values), (int) $average),
                'color' => '#F59E0B',
            ];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
                'toolbar' => [
                    'show' => true,
                    'tools' => [
                        'download' => true,
                        'selection' => false,
                        'zoom' => true,
                        'zoomin' => true,
                        'zoomout' => true,
                        'pan' => false,
                        'reset' => true,
                    ],
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $monthLabels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '12px',
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
                    'text' => 'Ingresos (COP)',
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => $average > 0 ? [3, 2] : [3],
                'dashArray' => $average > 0 ? [0, 5] : [0],
            ],
            'markers' => [
                'size' => $average > 0 ? [6, 0] : [6],
                'colors' => $average > 0 ? ['#10B981', '#F59E0B'] : ['#10B981'],
                'strokeColors' => '#fff',
                'strokeWidth' => 2,
                'hover' => [
                    'size' => 8,
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
            ],
            'legend' => [
                'position' => 'top',
                'fontFamily' => 'inherit',
            ],
            'grid' => [
                'show' => true,
                'borderColor' => '#e5e7eb',
                'strokeDashArray' => 1,
            ],
        ];
    }



    private function getProfessionalMonthlyChart(array $monthlyData): array
    {
        $monthLabels = array_column($monthlyData, 'label');
        $values = array_column($monthlyData, 'value');

        $nonZeroMonths = array_filter($values, fn($v) => $v > 0);
        $average = count($nonZeroMonths) > 0 ? array_sum($nonZeroMonths) / count($nonZeroMonths) : 0;
        $monthlyGoal = $average > 0 ? (int)($average * 1.2) : 0;

        $series = [
            [
                'name' => 'Mis Ingresos',
                'data' => $values,
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Mi Promedio',
                'data' => array_fill(0, count($values), (int) $average),
                'color' => '#F59E0B',
            ],
        ];

        if ($monthlyGoal > 0) {
            $series[] = [
                'name' => 'Meta Mensual',
                'data' => array_fill(0, count($values), $monthlyGoal),
                'color' => '#EF4444',
            ];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
                'toolbar' => [
                    'show' => true,
                    'tools' => [
                        'download' => true,
                        'selection' => false,
                        'zoom' => true,
                        'zoomin' => true,
                        'zoomout' => true,
                        'pan' => false,
                        'reset' => true,
                    ],
                ],
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $monthLabels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontSize' => '12px',
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
                    'text' => 'Ingresos (COP)',
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => $monthlyGoal > 0 ? [3, 2, 2] : [3, 2],
                'dashArray' => $monthlyGoal > 0 ? [0, 5, 3] : [0, 5],
            ],
            'markers' => [
                'size' => $monthlyGoal > 0 ? [6, 0, 0] : [6, 0],
                'colors' => $monthlyGoal > 0 ? ['#3B82F6', '#F59E0B', '#EF4444'] : ['#3B82F6', '#F59E0B'],
                'strokeColors' => '#fff',
                'strokeWidth' => 2,
                'hover' => [
                    'size' => 8,
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
            ],
            'legend' => [
                'position' => 'top',
                'fontFamily' => 'inherit',
            ],
            'grid' => [
                'show' => true,
                'borderColor' => '#e5e7eb',
                'strokeDashArray' => 1,
            ],
        ];
    }
}