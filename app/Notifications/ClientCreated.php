<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Client;

class ClientCreated extends Notification
{
    use Queueable;

    public function __construct(private Client $client) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Se ha creado un nuevo cliente: {$this->client->name}",
            'client_id' => $this->client->id,
            'type' => 'client_created'
        ];
    }
}
