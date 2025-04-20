<?php

namespace App\Observers;

use App\Models\Price;
use Filament\Notifications\Notification;

class PriceObserver
{
    
    public function updated(Price $price): void
    {
        // Verifica si el campo 'amount' fue cambiado
        // y envía una notificación si es así
        if ($price->wasChanged('amount')) {
            Notification::make()
                ->warning()
                ->title('Precio actualizado')
                ->body('El cambio en el precio solo afectará a las nuevas citas.')
                ->send();
        }
    }
}
