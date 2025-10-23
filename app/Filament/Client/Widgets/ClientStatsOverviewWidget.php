<?php

namespace App\Filament\Client\Widgets;

use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $clientId = Auth::guard('client')->id();

        // Citas totales
        $totalAppointments = Appointment::where('client_id', $clientId)->count();

        // Citas completadas
        $completedAppointments = Appointment::where('client_id', $clientId)
            ->where('status', 'completed')
            ->count();

        // Próximas citas
        $upcomingAppointments = Appointment::where('client_id', $clientId)
            ->where('start_time', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->count();

        // Última cita
        $lastAppointment = Appointment::where('client_id', $clientId)
            ->where('status', 'completed')
            ->orderBy('end_time', 'desc')
            ->first();

        $lastVisit = $lastAppointment
            ? Carbon::parse($lastAppointment->end_time)->diffForHumans()
            : 'Sin visitas';

        return [
            Stat::make('Total de Citas', $totalAppointments)
                ->description('Citas registradas')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Tratamientos Completados', $completedAppointments)
                ->description('Finalizados exitosamente')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Citas Programadas', $upcomingAppointments)
                ->description('Próximas citas')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Última Visita', $lastVisit)
                ->description($lastAppointment ? ($lastAppointment->service->name ?? 'Consulta') : 'Agenda tu primera cita')
                ->descriptionIcon('heroicon-o-heart')
                ->color('info'),
        ];
    }
}