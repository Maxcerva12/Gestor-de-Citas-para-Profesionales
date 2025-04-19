<?php

namespace App\Filament\Resources\PriceResource\Pages;

use App\Filament\Resources\PriceResource;
use App\Notifications\PriceCreated;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreatePrice extends CreateRecord
{
    protected static string $resource = PriceResource::class;

    protected function afterCreate(): void
    {
        $price = $this->getRecord();
        $user = Auth::user();

        // Solo enviar la notificación si hay un usuario autenticado
        if ($user && method_exists($user, 'notify')) {
            $user->notify(new PriceCreated($price));
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Precio creado')
            ->body('El precio ha sido creado correctamente')
            ->sendToDatabase(Auth::user());
    }
}
