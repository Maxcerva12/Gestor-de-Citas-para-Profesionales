<?php

namespace App\Filament\Client\Widgets;

use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    // Polling cada 30 segundos para actualizar stats
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $clientId = Auth::guard('client')->id();
        $cacheKey = "client_stats_{$clientId}";

        // Cache por 5 minutos
        $stats = Cache::remember($cacheKey, 300, function () use ($clientId) {
            // Una sola query con agregaciones para optimizar
            $appointmentStats = Appointment::where('client_id', $clientId)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                    DB::raw("SUM(CASE WHEN start_time >= NOW() AND status != 'canceled' THEN 1 ELSE 0 END) as upcoming")
                )
                ->first();

            // Última cita completada (solo campos necesarios)
            $lastAppointment = Appointment::where('client_id', $clientId)
                ->where('status', 'completed')
                ->select('end_time', 'service_id')
                ->with('service:id,name')
                ->orderBy('end_time', 'desc')
                ->first();

            return [
                'total' => $appointmentStats->total ?? 0,
                'completed' => $appointmentStats->completed ?? 0,
                'upcoming' => $appointmentStats->upcoming ?? 0,
                'lastAppointment' => $lastAppointment,
            ];
        });

        $lastVisit = $stats['lastAppointment']
            ? Carbon::parse($stats['lastAppointment']->end_time)->diffForHumans()
            : 'Sin visitas';

        return [
            Stat::make('Total de Citas', $stats['total'])
                ->description('Citas registradas')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Tratamientos Completados', $stats['completed'])
                ->description('Finalizados exitosamente')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Citas Programadas', $stats['upcoming'])
                ->description('Próximas citas')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Última Visita', $lastVisit)
                ->description($stats['lastAppointment'] ? ($stats['lastAppointment']->service->name ?? 'Consulta') : 'Agenda tu primera cita')
                ->descriptionIcon('heroicon-o-heart')
                ->color('info'),
        ];
    }
}