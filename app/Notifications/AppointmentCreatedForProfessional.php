<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;
use Filament\Notifications\Notification as FilamentNotification;

class AppointmentCreatedForProfessional extends Notification
{
    use Queueable;

    public function __construct(private Appointment $appointment) 
    {
        // Asegurar que las relaciones estén cargadas
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
            ->title('Nueva Cita Agendada')
            ->body("Tienes una nueva cita con {$clientName} para el " . 
                   $startTime->format('d/m/Y') . 
                   " a las " . $startTime->format('H:i'))
            ->icon('heroicon-o-calendar')
            ->iconColor('success')
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
