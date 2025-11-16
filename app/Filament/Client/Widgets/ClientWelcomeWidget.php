<?php

namespace App\Filament\Client\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Carbon\Carbon;

class ClientWelcomeWidget extends Widget
{
    protected static string $view = 'filament.client.widgets.client-welcome';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $client = Auth::guard('client')->user();
        $now = Carbon::now();

        // Próxima cita con eager loading
        $nextAppointment = Appointment::where('client_id', $client->id)
            ->where('start_time', '>=', $now)
            ->whereIn('status', ['pending', 'confirmed'])
            ->select('id', 'client_id', 'service_id', 'start_time')
            ->with('service:id,name')
            ->orderBy('start_time', 'asc')
            ->first();

        // Saludo según la hora
        $greeting = match (true) {
            $now->hour < 12 => 'Buenos días',
            $now->hour < 18 => 'Buenas tardes',
            default => 'Buenas noches'
        };

        return [
            'client' => $client,
            'greeting' => $greeting,
            'nextAppointment' => $nextAppointment,
            'currentDate' => $now->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
        ];
    }
}