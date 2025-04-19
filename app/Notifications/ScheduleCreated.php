<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Schedule;

class ScheduleCreated extends Notification
{
    use Queueable;

    public function __construct(private Schedule $schedule) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Se ha creado un nuevo horario para el profesional",
            'schedule_id' => $this->schedule->id,
            'type' => 'schedule_created'
        ];
    }
}
