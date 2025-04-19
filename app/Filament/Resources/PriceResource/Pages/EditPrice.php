<?php

namespace App\Filament\Resources\PriceResource\Pages;

use App\Filament\Resources\PriceResource;
use App\Notifications\PriceUpdated;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditPrice extends EditRecord
{
    protected static string $resource = PriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $price = $this->getRecord();
        $user = Auth::user();

        // Solo enviar la notificación si hay un usuario autenticado
        if ($user && method_exists($user, 'notify')) {
            $user->notify(new PriceUpdated($price));
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Precio actualizado')
            ->body('El precio ha sido actualizado correctamente')
            ->sendToDatabase(Auth::user());
    }
}
