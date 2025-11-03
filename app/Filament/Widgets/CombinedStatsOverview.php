<?php

namespace App\Filament\Widgets;

use App\Services\DashboardDataService;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class CombinedStatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getStats(): array
    {
        $currentUser = Auth::user();
        $canViewAllRevenue = $currentUser->hasRole('super_admin') ||
            $currentUser->hasPermissionTo('view_all_revenue');

        $service = app(DashboardDataService::class);
        $dashboardData = $service->getDashboardData($currentUser, $canViewAllRevenue);
        $statsData = $dashboardData['stats'];

        $stats = [];

        // Siempre mostrar Total de Profesionales y Clientes para todos
        $stats[] = Stat::make('Total de Profesionales', number_format($statsData['total_professionals'] ?? 0))
            ->description('Profesionales registrados')
            ->descriptionIcon('heroicon-m-user-group')
            ->chart($statsData['professionals_chart'] ?? [0])
            ->color('success');

        $stats[] = Stat::make('Total de Clientes', number_format($statsData['total_clients'] ?? 0))
            ->description('Clientes registrados')
            ->descriptionIcon('heroicon-m-user')
            ->chart($statsData['clients_chart'] ?? [0])
            ->color('info');

        // Facturas: título y descripción según el rol
        if ($canViewAllRevenue) {
            $stats[] = Stat::make('Facturas Generadas (Fundación)', number_format($statsData['total_invoices']))
                ->description('Facturas de toda la fundación (6 meses)')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($statsData['invoices_chart'] ?: [0])
                ->color('success');
        } else {
            $stats[] = Stat::make('Mis Facturas Generadas', number_format($statsData['total_invoices']))
                ->description('Facturas de mis servicios (6 meses)')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($statsData['invoices_chart'] ?: [0])
                ->color('warning');
        }

        return $stats;
    }
}
