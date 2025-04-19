<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class AppointmentCreated extends Notification
{
    use Queueable;

    public function __construct(private Appointment $appointment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Se ha creado una nueva cita para el {$this->appointment->date}",
            'appointment_id' => $this->appointment->id,
            'type' => 'appointment_created'
        ];
    }
}
