<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopProfessionalsChart extends ApexChartWidget
{
    use HasWidgetShield;

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'topProfessionalsChart';

    /**
     * Widget Height
     */
    protected static ?int $height = 400;

    /**
     * Sort
     */
    protected static ?int $sort = 4;

    /**
     * Widget Title
     */
    protected function getHeading(): string
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        return $canViewAllRevenue
            ? 'Top Profesionales por Ingresos'
            : 'Mis Servicios Más Rentables';
    }

    /**
     * Widget Subheading
     */
    protected function getSubHeading(): ?string
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        return $canViewAllRevenue
            ? 'Ranking de profesionales con mayores ingresos'
            : 'Top 10 de mis servicios ordenados por ingresos';
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     */
    protected function getOptions(): array
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        if ($canViewAllRevenue) {
            return $this->getFoundationChart();
        } else {
            return $this->getProfessionalServicesChart($currentUser);
        }
    }

    /**
     * Chart para Super Admin - Top Profesionales por Ingresos
     */
    private function getFoundationChart(): array
    {
        // Obtener los ingresos por profesional
        $professionalsRevenue = User::select('users.id', 'users.name')
            ->leftJoin('invoices', 'users.id', '=', 'invoices.user_id')
            ->selectRaw('COALESCE(SUM(invoices.total_amount), 0) as total_revenue')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $labels = $professionalsRevenue->pluck('name')->toArray();
        $data = $professionalsRevenue->pluck('total_revenue')->map(fn($value) => (float) $value / 100)->toArray();

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
            'colors' => ['#3B82F6'],
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
                    'formatter' => null, // Usar formato de número básico
                ],
            ],
        ];
    }

    /**
     * Chart para Profesionales - Sus Servicios Más Rentables
     */
    private function getProfessionalServicesChart($user): array
    {
        // Obtener los ingresos por servicio del profesional
        $servicesRevenue = Service::select('services.id', 'services.name')
            ->leftJoin('appointments', 'services.id', '=', 'appointments.service_id')
            ->leftJoin('invoices', function ($join) use ($user) {
                $join->on('appointments.id', '=', 'invoices.appointment_id')
                    ->where('invoices.user_id', '=', $user->id);
            })
            ->where('appointments.user_id', $user->id)
            ->selectRaw('COALESCE(SUM(invoices.total_amount), 0) as total_revenue')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $labels = $servicesRevenue->pluck('name')->toArray();
        $data = $servicesRevenue->pluck('total_revenue')->map(fn($value) => (float) $value / 100)->toArray();

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
            'colors' => ['#10B981'],
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
                    'formatter' => null, // Usar formato de número básico
                ],
            ],
        ];
    }
}