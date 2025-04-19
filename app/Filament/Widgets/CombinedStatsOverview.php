<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CombinedStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Definir el período (últimos 30 días)
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Total de Profesionales
        $totalProfessionals = User::whereHas('roles')->count();

        // Datos históricos para el gráfico de profesionales (registros por día)
        $professionalsData = User::whereHas('roles')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total')
            ->map(fn($value) => (int) $value) // Castear a entero
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
            ->map(fn($value) => (int) $value) // Castear a entero
            ->toArray();

        // Ingresos por Citas (todas las citas con estado 'paid')
        $paidAppointments = Appointment::join('prices', 'appointments.price_id', '=', 'prices.id')
            ->where('appointments.payment_status', 'paid')
            ->get();

        $totalRevenue = 0;
        foreach ($paidAppointments as $appointment) {
            $totalRevenue += $appointment->price->amount;
        }

        // Ingresos diarios (para el gráfico) - agrupados por fecha
        $revenues = Appointment::join('prices', 'appointments.price_id', '=', 'prices.id')
            ->select(
                DB::raw('DATE(appointments.start_time) as date'),
                DB::raw('COUNT(appointments.id) as appointment_count'),
                DB::raw('SUM(prices.amount) as total')
            )
            ->where('appointments.payment_status', 'paid')
            ->whereBetween('appointments.start_time', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totals = $revenues->pluck('total')
            ->map(fn($value) => (float) $value) // Castear a float para ingresos
            ->toArray();

        return [
            // Bloque 1: Total de Profesionales
            Stat::make('Total de Profesionales', number_format($totalProfessionals))
                ->description('Profesionales registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($professionalsData ?: [0]) // Fallback a [0] si está vacío
                ->color('success'),

            // Bloque 2: Total de Clientes
            Stat::make('Total de Clientes', number_format($totalClients))
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-m-user')
                ->chart($clientsData ?: [0]) // Fallback a [0] si está vacío
                ->color('info'),

            // Bloque 3: Ingresos por Citas
            Stat::make('Ingresos por Citas', '€' . number_format($totalRevenue, 2))
                ->description('Total de citas pagadas')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->chart($totals ?: [0]) // Fallback a [0] si está vacío
                ->color('primary'),
        ];
    }
}
