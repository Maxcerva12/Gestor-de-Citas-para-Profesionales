<?php

namespace App\Observers;

use App\Models\User;
use Filament\Notifications\Notification;
use App\Notifications\UserUpdated;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Notification::Make()
            ->title('User Created')
            ->body('A new user has been created.')
            ->send($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Enviar notificación usando el canal de base de datos
        $user->notify(new UserUpdated($user));

        // Mostrar notificación en la interfaz de Filament
        Notification::make()
            ->success()
            ->title('Usuario Actualizado')
            ->body('El usuario ' . $user->name . ' ha sido actualizado.')
            ->send();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
