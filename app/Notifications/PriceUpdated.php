<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Price;

class PriceUpdated extends Notification
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
            'message' => "Se ha actualizado el precio: {$this->price->name}",
            'price_id' => $this->price->id,
            'type' => 'price_updated'
        ];
    }
}
