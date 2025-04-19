<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Notifications\AppointmentUpdated;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $appointment = $this->getRecord();
        $user = Auth::user();

        // Enviar notificación al usuario autenticado
        if ($user && method_exists($user, 'notify')) {
            $user->notify(new AppointmentUpdated($appointment));
        }

        // También notificar al cliente si existe
        if ($appointment->client && method_exists($appointment->client, 'notify')) {
            $appointment->client->notify(new AppointmentUpdated($appointment));
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cita actualizada')
            ->body('La cita ha sido actualizada correctamente')
            ->sendToDatabase(Auth::user());
    }
}
