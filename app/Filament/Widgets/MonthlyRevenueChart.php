<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        if ($canViewAllRevenue) {
            return $this->getFoundationMonthlyChart();
        } else {
            return $this->getProfessionalMonthlyChart($currentUser);
        }
    }

    private function getFoundationMonthlyChart(): array
    {
        // Obtener datos de los últimos 12 meses
        $monthlyData = [];
        $monthLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);

            // Ingresos por facturas del mes
            $invoiceAmount = Invoice::where('state', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount') / 100; // Convertir de centavos a pesos

            // Ingresos por citas sin factura del mes
            $appointmentAmount = Appointment::where('payment_status', 'paid')
                ->whereDoesntHave('invoices', function ($query) {
                    $query->where('state', 'paid');
                })
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('service_price');

            $totalAmount = $invoiceAmount + $appointmentAmount;

            $monthlyData[] = (int) $totalAmount;
            $monthLabels[] = $date->locale('es')->isoFormat('MMM YYYY');
        }

        // Calcular el promedio solo de los valores mayores a 0
        $nonZeroValues = array_filter($monthlyData, function ($value) {
            return $value > 0;
        });
        $average = count($nonZeroValues) > 0 ? array_sum($nonZeroValues) / count($nonZeroValues) : 0;

        // Encontrar el valor máximo para establecer una escala adecuada
        $maxValue = max($monthlyData);

        $series = [
            [
                'name' => 'Ingresos Totales',
                'data' => $monthlyData,
                'color' => '#10B981',
            ]
        ];

        // Solo agregar la línea de promedio si hay datos significativos
        if ($average > 0) {
            $series[] = [
                'name' => 'Promedio',
                'data' => array_fill(0, count($monthlyData), (int) $average),
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
                'dashArray' => $average > 0 ? [0, 5] : [0], // Línea sólida para ingresos, punteada para promedio
            ],
            'markers' => [
                'size' => $average > 0 ? [6, 0] : [6], // Marcadores solo en la línea de ingresos
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



    private function getProfessionalMonthlyChart($user): array
    {
        // Obtener datos de los últimos 12 meses para el profesional
        $monthlyData = [];
        $monthLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);

            // Ingresos por facturas del profesional
            $invoiceAmount = Invoice::where('user_id', $user->id)
                ->where('state', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount') / 100;

            // Ingresos por citas sin factura del profesional
            $appointmentAmount = Appointment::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->whereDoesntHave('invoices', function ($query) {
                    $query->where('state', 'paid');
                })
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('service_price');

            $totalAmount = $invoiceAmount + $appointmentAmount;

            $monthlyData[] = (int) $totalAmount;
            $monthLabels[] = $date->locale('es')->isoFormat('MMM YYYY');
        }

        // Calcular el promedio solo de los meses con ingresos > 0
        $nonZeroMonths = array_filter($monthlyData, function ($value) {
            return $value > 0;
        });
        $average = count($nonZeroMonths) > 0 ? array_sum($nonZeroMonths) / count($nonZeroMonths) : 0;

        // Obtener la meta mensual (si existe)
        $monthlyGoal = $this->getMonthlyGoal($user, $average);

        $series = [
            [
                'name' => 'Mis Ingresos',
                'data' => $monthlyData,
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Mi Promedio',
                'data' => array_fill(0, count($monthlyData), (int) $average),
                'color' => '#F59E0B',
            ],
        ];

        // Agregar línea de meta si existe
        if ($monthlyGoal > 0) {
            $series[] = [
                'name' => 'Meta Mensual',
                'data' => array_fill(0, count($monthlyData), $monthlyGoal),
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

    private function getMonthlyGoal($user, float $currentAverage = 0): int
    {
        // Si no hay promedio actual, no mostrar meta
        if ($currentAverage <= 0) {
            return 0;
        }

        // Calcular meta como 20% superior al promedio actual
        // Esto es más realista que usar datos históricos cuando hay pocos datos
        return (int) ($currentAverage * 1.2); // Meta 20% superior al promedio actual
    }
    private function calculateHistoricalAverage($user): float
    {
        // Calcular promedio de los últimos 6 meses (excluyendo los últimos 2 para evitar sesgos)
        $averageData = [];

        for ($i = 8; $i >= 3; $i--) {
            $date = Carbon::now()->subMonths($i);

            $invoiceAmount = Invoice::where('user_id', $user->id)
                ->where('state', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount') / 100;

            $appointmentAmount = Appointment::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->whereDoesntHave('invoices', function ($query) {
                    $query->where('state', 'paid');
                })
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('service_price');

            $averageData[] = $invoiceAmount + $appointmentAmount;
        }

        return count($averageData) > 0 ? array_sum($averageData) / count($averageData) : 0;
    }
}