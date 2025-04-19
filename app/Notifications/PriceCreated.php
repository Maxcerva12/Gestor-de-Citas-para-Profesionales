<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Price;

class PriceCreated extends Notification
{
    use Queueable;

    public function __construct(private Price $price) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Se ha creado un nuevo precio: {$this->price->name}",
            'price_id' => $this->price->id,
            'type' => 'price_created'
        ];
    }
}
