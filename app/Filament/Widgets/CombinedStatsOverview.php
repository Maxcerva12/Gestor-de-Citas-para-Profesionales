<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


class CombinedStatsOverview extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
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
        ];
    }
}
