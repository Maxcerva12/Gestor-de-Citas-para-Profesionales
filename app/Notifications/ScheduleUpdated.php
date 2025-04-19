<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Schedule;

class ScheduleUpdated extends Notification
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
            'message' => "Se ha actualizado el horario del profesional",
            'schedule_id' => $this->schedule->id,
            'type' => 'schedule_updated'
        ];
    }
}
