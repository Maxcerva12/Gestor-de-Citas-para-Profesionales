<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class RevenueOverviewWidget extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        $service = app(DashboardDataService::class);
        $dashboardData = $service->getDashboardData($currentUser, $canViewAllRevenue);
        $revenueData = $dashboardData['revenue'];

        if ($canViewAllRevenue) {
            return $this->getFoundationRevenueStats($revenueData);
        }

        return $this->getProfessionalRevenueStats($revenueData);
    }

    private function getFoundationRevenueStats(array $revenueData): array
    {
        $totalRevenue = $revenueData['total_revenue'];
        $monthlyRevenue = $revenueData['monthly_revenue'];
        $growthPercentage = $revenueData['growth_percentage'];
        $dailyRevenue = $revenueData['daily_chart'];
        $activeProfessionals = $revenueData['active_professionals'];

        return [
            Stat::make('Ingresos Totales de la Fundación', '$' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total acumulado de todos los ingresos')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($dailyRevenue ?: [0])
                ->color('success'),

            Stat::make('Ingresos del Mes Actual', '$' . number_format($monthlyRevenue, 0, ',', '.'))
                ->description(($growthPercentage >= 0 ? '+' : '') . number_format($growthPercentage, 1) . '% vs mes anterior')
                ->descriptionIcon($growthPercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($dailyRevenue ?: [0])
                ->color($growthPercentage >= 0 ? 'success' : 'danger'),

            Stat::make('Número de Profesionales Activos', $activeProfessionals)
                ->description('Profesionales con roles asignados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }

    private function getProfessionalRevenueStats(array $revenueData): array
    {
        $totalRevenue = $revenueData['total_revenue'];
        $monthlyRevenue = $revenueData['monthly_revenue'];
        $dailyRevenue = $revenueData['daily_chart'];
        $appointmentsThisMonth = $revenueData['appointments_this_month'];

        return [
            Stat::make('Mis Ingresos Totales', '$' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total de mis servicios prestados')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($dailyRevenue ?: [0])
                ->color('success'),

            Stat::make('Ingresos del Mes Actual', '$' . number_format($monthlyRevenue, 0, ',', '.'))
                ->description('Ingresos de este mes')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Citas Atendidas este Mes', $appointmentsThisMonth)
                ->description('Servicios completados')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),
        ];
    }


}