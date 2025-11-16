<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;

class MonthlyRevenueChart extends ApexChartWidget
{
    use HasWidgetShield;

    protected static ?string $chartId = 'monthlyRevenueChart';
    protected static ?string $heading = 'Evolución Mensual de Ingresos';
    
    /**
     * Formatear número para mostrar K, M, B
     */
    private function formatNumber(float $number): float
    {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1);
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1);
        } elseif ($number >= 1000) {
            return round($number / 1000, 1);
        }
        return round($number, 0);
    }
    
    /**
     * Obtener unidad (K, M, B) basada en el valor máximo
     */
    private function getUnit(array $values): string
    {
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
            'last_7_days' => 'Evolución de Ingresos - Últimos 7 días',
            'last_4_weeks' => 'Evolución de Ingresos - Últimas 4 semanas',
            'last_6_months' => 'Evolución de Ingresos - Últimos 6 meses',
            'last_5_years' => 'Evolución de Ingresos - Últimos 5 años',
            default => 'Evolución de Ingresos',
        };
    }

    protected function getOptions(): array
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        $service = app(DashboardDataService::class);
        
        // Obtener datos según el filtro seleccionado
        if ($this->filter === 'last_4_weeks') {
            $chartData = $service->getWeeklyRevenueData($currentUser, $canViewAllRevenue, $this->filter);
        } elseif ($this->filter === 'last_6_months') {
            $chartData = $service->getFilteredRevenueData($currentUser, $canViewAllRevenue, $this->filter);
        } elseif ($this->filter === 'last_5_years') {
            $chartData = $service->getYearlyRevenueData($currentUser, $canViewAllRevenue, $this->filter);
        } else {
            $chartData = $service->getDailyRevenueData($currentUser, $canViewAllRevenue, $this->filter);
        }

        if ($canViewAllRevenue) {
            return $this->getFoundationChart($chartData);
        }

        return $this->getProfessionalChart($chartData);
    }

    private function getFoundationChart(array $chartData): array
    {
        $labels = array_column($chartData, 'label');
        $rawValues = array_column($chartData, 'value');
        
        // Determinar unidad para mostrar en títulos, pero usar valores reales en datos
        $unit = $this->getUnit($rawValues);
        $values = array_map(fn($value) => $this->formatNumber($value), $rawValues);

        $nonZeroRawValues = array_filter($rawValues, fn($v) => $v > 0);
        $rawAverage = count($nonZeroRawValues) > 0 ? array_sum($nonZeroRawValues) / count($nonZeroRawValues) : 0;

        // Crear series con valores reales para tooltip completo
        $series = [
            [
                'name' => 'Ingresos' . ($unit ? ' (' . $unit . ')' : ''),
                'data' => $rawValues, // Valores reales para tooltip completo
                'color' => '#10B981',
            ]
        ];

        if ($rawAverage > 0) {
            $series[] = [
                'name' => 'Promedio' . ($unit ? ' (' . $unit . ')' : ''),
                'data' => array_fill(0, count($rawValues), round($rawAverage, 0)),
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
                'categories' => $labels,
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
                    'text' => 'Ingresos' . ($unit ? ' (' . $unit . ')' : ' (COP)'),
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => $rawAverage > 0 ? [3, 2] : [3],
                'dashArray' => $rawAverage > 0 ? [0, 5] : [0],
            ],
            'markers' => [
                'size' => $rawAverage > 0 ? [6, 0] : [6],
                'colors' => $rawAverage > 0 ? ['#10B981', '#F59E0B'] : ['#10B981'],
                'strokeColors' => '#fff',
                'strokeWidth' => 2,
                'hover' => [
                    'size' => 8,
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
                'y' => [
                    'formatter' => 'function(value) {
                        return new Intl.NumberFormat("es-CO", {
                            style: "currency",
                            currency: "COP",
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value);
                    }'
                ]
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

    private function getProfessionalChart(array $chartData): array
    {
        $labels = array_column($chartData, 'label');
        $rawValues = array_column($chartData, 'value');
        
        // Determinar unidad para mostrar en títulos, pero usar valores reales en datos
        $unit = $this->getUnit($rawValues);
        $values = array_map(fn($value) => $this->formatNumber($value), $rawValues);

        $nonZeroRawValues = array_filter($rawValues, fn($v) => $v > 0);
        $rawAverage = count($nonZeroRawValues) > 0 ? array_sum($nonZeroRawValues) / count($nonZeroRawValues) : 0;
        $rawMonthlyGoal = $rawAverage > 0 ? $rawAverage * 1.2 : 0;

        $series = [
            [
                'name' => 'Mis Ingresos' . ($unit ? ' (' . $unit . ')' : ''),
                'data' => $rawValues, // Valores reales para tooltip completo
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Mi Promedio' . ($unit ? ' (' . $unit . ')' : ''),
                'data' => array_fill(0, count($rawValues), round($rawAverage, 0)),
                'color' => '#F59E0B',
            ],
        ];

        if ($rawMonthlyGoal > 0) {
            $series[] = [
                'name' => 'Meta Mensual' . ($unit ? ' (' . $unit . ')' : ''),
                'data' => array_fill(0, count($rawValues), round($rawMonthlyGoal, 0)),
                'color' => '#EF4444',
            ];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 250,
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
                'categories' => $labels,
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
                    'text' => 'Ingresos' . ($unit ? ' (' . $unit . ')' : ' (COP)'),
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => $rawMonthlyGoal > 0 ? [3, 2, 2] : [3, 2],
                'dashArray' => $rawMonthlyGoal > 0 ? [0, 5, 3] : [0, 5],
            ],
            'markers' => [
                'size' => $rawMonthlyGoal > 0 ? [6, 0, 0] : [6, 0],
                'colors' => $rawMonthlyGoal > 0 ? ['#3B82F6', '#F59E0B', '#EF4444'] : ['#3B82F6', '#F59E0B'],
                'strokeColors' => '#fff',
                'strokeWidth' => 2,
                'hover' => [
                    'size' => 8,
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
                'y' => [
                    'formatter' => 'function(value) {
                        return new Intl.NumberFormat("es-CO", {
                            style: "currency",
                            currency: "COP",
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value);
                    }'
                ]
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
