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

        // Definir el período (últimos 30 días)
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Total de Profesionales
        $totalProfessionals = User::count();

        // Datos históricos para el gráfico de profesionales (registros por día)
        $professionalsData = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value)
            ->toArray();

        // Total de Clientes
        $totalClients = Client::count();

        // Datos históricos para el gráfico de clientes (registros por día)
        $clientsData = Client::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
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
        // Total de facturas generadas este mes
        $currentMonth = Carbon::now();
        $totalInvoicesThisMonth = Invoice::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        // Datos históricos para el gráfico (facturas por día, últimos 30 días)
        $invoicesData = Invoice::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value)
            ->toArray();

        return Stat::make('Facturas Generadas (Fundación)', number_format($totalInvoicesThisMonth))
            ->description('Facturas de toda la fundación este mes')
            ->descriptionIcon('heroicon-m-document-text')
            ->chart($invoicesData ?: [0])
            ->color('success');
    }

    private function getProfessionalInvoiceStats($user, $startDate, $endDate): Stat
    {
        // Total de facturas generadas por el profesional este mes
        $currentMonth = Carbon::now();
        $totalInvoicesThisMonth = Invoice::where('user_id', $user->id)
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        // Datos históricos para el gráfico (facturas del profesional por día, últimos 30 días)
        $invoicesData = Invoice::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value)
            ->toArray();

        return Stat::make('Mis Facturas Generadas', number_format($totalInvoicesThisMonth))
            ->description('Facturas de mis servicios este mes')
            ->descriptionIcon('heroicon-m-document-text')
            ->chart($invoicesData ?: [0])
            ->color('info');
    }
}
