<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Notifications\ClientUpdated;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Obtener el cliente que está siendo editado
        $client = $this->getRecord();

        // Enviar notificación a la base de datos
        $client->notify(new ClientUpdated($client));
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cliente actualizado')
            ->body('El cliente ha sido actualizado correctamente')
            ->sendToDatabase(Auth::user());
    }
}
