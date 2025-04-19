<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Notifications\AppointmentCreated;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function afterCreate(): void
    {
        $appointment = $this->getRecord();
        $user = Auth::user();

        // Enviar notificación al usuario autenticado
        if ($user && method_exists($user, 'notify')) {
            $user->notify(new AppointmentCreated($appointment));
        }

        // También notificar al cliente si existe
        if ($appointment->client && method_exists($appointment->client, 'notify')) {
            $appointment->client->notify(new AppointmentCreated($appointment));
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cita creada')
            ->body('La cita ha sido creada correctamente')
            ->sendToDatabase(Auth::user());
    }
}
