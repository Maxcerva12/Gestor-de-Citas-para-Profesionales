<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Notifications\ClientCreated;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function afterCreate(): void
    {
        // Obtener el cliente recién creado
        $client = $this->getRecord();

        // Enviar notificación a la base de datos
        $client->notify(new ClientCreated($client));
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cliente creado')
            ->body('El cliente ha sido creado correctamente')
            ->sendToDatabase(Auth::user());
    }
}
