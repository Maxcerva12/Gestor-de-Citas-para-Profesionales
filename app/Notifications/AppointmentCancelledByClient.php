<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;
use Filament\Notifications\Notification as FilamentNotification;

class AppointmentCancelledByClient extends Notification
{
    use Queueable;

    public function __construct(private Appointment $appointment) 
    {
        $this->appointment->loadMissing(['client', 'user']);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $clientName = $this->appointment->client?->name ?? 'Cliente desconocido';
        $startTime = $this->appointment->start_time;
        
        return FilamentNotification::make()
            ->title('Cita Cancelada por Cliente')
            ->body("El cliente {$clientName} ha cancelado su cita del " . 
                   $startTime->format('d/m/Y') . 
                   " a las " . $startTime->format('H:i') .
                   ($this->appointment->cancellation_reason ? "\nMotivo: " . $this->appointment->cancellation_reason : ''))
            ->icon('heroicon-o-x-circle')
            ->iconColor('danger')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Ver cita')
                    ->url(fn (): string => route('filament.admin.resources.appointments.edit', ['record' => $this->appointment->id]))
                    ->button(),
            ])
            ->getDatabaseMessage();
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
