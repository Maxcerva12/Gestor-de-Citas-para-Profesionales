<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Spatie\Permission\Models\Role;

class RoleUpdated extends Notification
{
    use Queueable;

    public function __construct(private Role $role) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Se ha actualizado el rol: {$this->role->name}",
            'role_id' => $this->role->id,
            'type' => 'role_updated'
        ];
    }
}
