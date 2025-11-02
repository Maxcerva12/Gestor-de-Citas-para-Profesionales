<?php

namespace App\Observers;

use App\Models\User;
use Filament\Notifications\Notification;
use App\Notifications\UserUpdated;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Limpiar caché de filtros cuando se crea un usuario
        $this->clearFiltersCache();
        
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
        // Limpiar caché de filtros si cambiaron profesión o especialidad
        if ($user->isDirty(['profession', 'especialty'])) {
            $this->clearFiltersCache();
        }
        
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
        // Limpiar caché de filtros cuando se elimina un usuario
        $this->clearFiltersCache();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Limpiar caché de filtros cuando se restaura un usuario
        $this->clearFiltersCache();
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // Limpiar caché de filtros cuando se elimina permanentemente
        $this->clearFiltersCache();
    }

    /**
     * Limpia el caché de los filtros de profesiones y especialidades
     */
    private function clearFiltersCache(): void
    {
        Cache::forget('user_professions');
        Cache::forget('user_especialties');
    }
}
