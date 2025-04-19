<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use App\Notifications\UserUpdated;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        // Obtener el usuario que está siendo editado
        $user = $this->getRecord();
        
        // Enviar notificación a la base de datos
        $user->notify(new UserUpdated($user));

        return Notification::make()
            ->success()
            ->title('Usuario actualizado')
            ->body('El usuario ha sido actualizado correctamente')
            ->sendToDatabase(Auth::user());
            // ->persistent();
    }

    protected function afterSave(): void
    {
        // La notificación ya se envía en getSavedNotification
    }
}
