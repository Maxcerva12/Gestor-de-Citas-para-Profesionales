<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        // Definir el período (últimos 6 meses para ver mejor los datos)
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        // Total de Profesionales
        $totalProfessionals = User::count();

        // Datos históricos para el gráfico de profesionales (registros por semana para mejor visualización)
        $professionalsData = User::select(
            DB::raw('EXTRACT(WEEK FROM created_at) as week'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('week', 'year')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value)
            ->toArray();

        // Total de Clientes
        $totalClients = Client::count();

        // Datos históricos para el gráfico de clientes (registros por semana)
        $clientsData = Client::select(
            DB::raw('EXTRACT(WEEK FROM created_at) as week'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('week', 'year')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value)
            ->toArray();

        // Facturas generadas - diferente según permisos
        if ($canViewAllRevenue) {
            $invoiceStats = $this->getFoundationInvoiceStats($startDate, $endDate);
        } else {
            $invoiceStats = $this->getProfessionalInvoiceStats($currentUser, $startDate, $endDate);
        }

        return [
            // Bloque 1: Total de Profesionales
            Stat::make('Total de Profesionales', number_format($totalProfessionals))
                ->description('Profesionales registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($professionalsData ?: [0])
                ->color('success'),

            // Bloque 2: Total de Clientes
            Stat::make('Total de Clientes', number_format($totalClients))
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-m-user')
                ->chart($clientsData ?: [0])
                ->color('info'),

            // Bloque 3: Facturas Generadas
            $invoiceStats,
        ];
    }

    private function getFoundationInvoiceStats($startDate, $endDate): Stat
    {
        // Total de facturas generadas en los últimos 6 meses
        $totalInvoices = Invoice::whereBetween('created_at', [$startDate, $endDate])->count();

        // Datos históricos para el gráfico (facturas por semana, últimos 6 meses)
        $invoicesData = Invoice::select(
            DB::raw('EXTRACT(WEEK FROM created_at) as week'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('week', 'year')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value)
            ->toArray();

        return Stat::make('Facturas Generadas (Fundación)', number_format($totalInvoices))
            ->description('Facturas de toda la fundación (6 meses)')
            ->descriptionIcon('heroicon-m-document-text')
            ->chart($invoicesData ?: [0])
            ->color('success');
    }

    private function getProfessionalInvoiceStats($user, $startDate, $endDate): Stat
    {
        // Total de facturas generadas por el profesional en los últimos 6 meses
        $totalInvoices = Invoice::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Datos históricos para el gráfico (facturas del profesional por semana, últimos 6 meses)
        $invoicesData = Invoice::select(
            DB::raw('EXTRACT(WEEK FROM created_at) as week'),
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('count(*) as total')
        )
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('week', 'year')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value)
            ->toArray();

        return Stat::make('Mis Facturas Generadas', number_format($totalInvoices))
            ->description('Facturas de mis servicios (6 meses)')
            ->descriptionIcon('heroicon-m-document-text')
            ->chart($invoicesData ?: [0])
            ->color('info');
    }
}
