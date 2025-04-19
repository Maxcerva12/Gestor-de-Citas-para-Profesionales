<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserCreated extends Notification
{
    use Queueable;

    public function __construct(private User $user) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Se ha creado un nuevo usuario: {$this->user->name}",
            'user_id' => $this->user->id,
            'type' => 'user_created'
        ];
    }
}
