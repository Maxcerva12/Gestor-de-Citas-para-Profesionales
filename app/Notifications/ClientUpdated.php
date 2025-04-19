<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Client;

class ClientUpdated extends Notification
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
            'message' => "Se ha actualizado el cliente: {$this->client->name}",
            'client_id' => $this->client->id,
            'type' => 'client_updated'
        ];
    }
}
