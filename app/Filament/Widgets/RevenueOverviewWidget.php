<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        // Definir el período (últimos 30 días para los gráficos)
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        if ($canViewAllRevenue) {
            // Super admin ve ingresos totales de la fundación
            return $this->getFoundationRevenueStats($startDate, $endDate);
        } else {
            // Profesionales ven solo sus ingresos personales
            return $this->getProfessionalRevenueStats($currentUser, $startDate, $endDate);
        }
    }

    private function getFoundationRevenueStats($startDate, $endDate): array
    {
        // Total de ingresos por facturas pagadas (este es el ingreso real)
        $totalInvoiceRevenue = Invoice::where('state', 'paid')
            ->sum('total_amount');

        // Total de ingresos por citas pagadas SIN factura asociada
        $totalAppointmentRevenue = Appointment::where('payment_status', 'paid')
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('state', 'paid');
            })
            ->sum('service_price');

        // Total consolidado (convertir de centavos a pesos solo para facturas)
        $totalRevenue = ($totalInvoiceRevenue / 100) + $totalAppointmentRevenue;

        // Ingresos del mes actual
        $currentMonthInvoiceRevenue = Invoice::where('state', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $currentMonthAppointmentRevenue = Appointment::where('payment_status', 'paid')
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('state', 'paid');
            })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('service_price');

        $monthlyRevenue = ($currentMonthInvoiceRevenue / 100) + $currentMonthAppointmentRevenue;

        // Datos para gráfico (últimos 30 días)
        $dailyRevenue = $this->getDailyRevenueData($startDate, $endDate);

        // Calcular crecimiento mensual
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthInvoiceRevenue = Invoice::where('state', 'paid')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->sum('total_amount');

        $previousMonthAppointmentRevenue = Appointment::where('payment_status', 'paid')
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('state', 'paid');
            })
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->sum('service_price');

        $previousMonthTotal = ($previousMonthInvoiceRevenue / 100) + $previousMonthAppointmentRevenue;

        $growthPercentage = $previousMonthTotal > 0
            ? (($monthlyRevenue - $previousMonthTotal) / $previousMonthTotal) * 100
            : 0;

        return [
            Stat::make('Ingresos Totales de la Fundación', '$' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total acumulado de todos los ingresos')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($dailyRevenue ?: [0])
                ->color('success'),

            Stat::make('Ingresos del Mes Actual', '$' . number_format($monthlyRevenue, 0, ',', '.'))
                ->description(($growthPercentage >= 0 ? '+' : '') . number_format($growthPercentage, 1) . '% vs mes anterior')
                ->descriptionIcon($growthPercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($this->getMonthlyRevenueData())
                ->color($growthPercentage >= 0 ? 'success' : 'danger'),

            Stat::make('Número de Profesionales Activos', User::whereHas('roles')->count())
                ->description('Profesionales con roles asignados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }

    private function getProfessionalRevenueStats($user, $startDate, $endDate): array
    {
        // Total de ingresos del profesional por facturas (ingreso real con IVA)
        $totalInvoiceRevenue = Invoice::where('user_id', $user->id)
            ->where('state', 'paid')
            ->sum('total_amount');

        // Total de ingresos por citas SIN factura asociada
        $totalAppointmentRevenue = Appointment::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('state', 'paid');
            })
            ->sum('service_price');

        // Total consolidado
        $totalRevenue = ($totalInvoiceRevenue / 100) + $totalAppointmentRevenue;

        // Ingresos del mes actual
        $currentMonthInvoiceRevenue = Invoice::where('user_id', $user->id)
            ->where('state', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $currentMonthAppointmentRevenue = Appointment::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('state', 'paid');
            })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('service_price');

        $monthlyRevenue = ($currentMonthInvoiceRevenue / 100) + $currentMonthAppointmentRevenue;

        // Datos para gráfico del profesional
        $dailyRevenue = $this->getProfessionalDailyRevenue($user->id, $startDate, $endDate);

        // Número de citas atendidas este mes
        $appointmentsThisMonth = Appointment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

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

    private function getDailyRevenueData($startDate, $endDate): array
    {
        // Ingresos por facturas por día
        $invoiceRevenue = Invoice::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('state', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Ingresos por citas SIN factura por día
        $appointmentRevenue = Appointment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(service_price) as total')
        )
            ->where('payment_status', 'paid')
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('state', 'paid');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Combinar ambos tipos de ingresos
        $combinedRevenue = [];
        $period = Carbon::parse($startDate);

        while ($period->lte($endDate)) {
            $dateStr = $period->toDateString();
            $invoiceAmount = isset($invoiceRevenue[$dateStr]) ? $invoiceRevenue[$dateStr]->total / 100 : 0;
            $appointmentAmount = isset($appointmentRevenue[$dateStr]) ? $appointmentRevenue[$dateStr]->total : 0;

            $combinedRevenue[] = (int) ($invoiceAmount + $appointmentAmount);
            $period->addDay();
        }

        return $combinedRevenue;
    }

    private function getProfessionalDailyRevenue($userId, $startDate, $endDate): array
    {
        // Similar al método anterior pero filtrado por usuario
        $invoiceRevenue = Invoice::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('user_id', $userId)
            ->where('state', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $appointmentRevenue = Appointment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(service_price) as total')
        )
            ->where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->whereDoesntHave('invoices', function ($query) {
                $query->where('state', 'paid');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $combinedRevenue = [];
        $period = Carbon::parse($startDate);

        while ($period->lte($endDate)) {
            $dateStr = $period->toDateString();
            $invoiceAmount = isset($invoiceRevenue[$dateStr]) ? $invoiceRevenue[$dateStr]->total / 100 : 0;
            $appointmentAmount = isset($appointmentRevenue[$dateStr]) ? $appointmentRevenue[$dateStr]->total : 0;

            $combinedRevenue[] = (int) ($invoiceAmount + $appointmentAmount);
            $period->addDay();
        }

        return $combinedRevenue;
    }

    private function getMonthlyRevenueData(): array
    {
        // Obtener ingresos de los últimos 6 meses
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $invoiceAmount = Invoice::where('state', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount') / 100;

            $appointmentAmount = Appointment::where('payment_status', 'paid')
                ->whereDoesntHave('invoices', function ($query) {
                    $query->where('state', 'paid');
                })
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('service_price');

            $monthlyData[] = (int) ($invoiceAmount + $appointmentAmount);
        }

        return $monthlyData;
    }
}