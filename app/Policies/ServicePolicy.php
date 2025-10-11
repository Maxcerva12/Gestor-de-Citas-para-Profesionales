<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Los usuarios pueden ver servicios
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        // Super admin puede ver todos los servicios
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Los usuarios solo pueden ver sus propios servicios
        return $service->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Todos los usuarios pueden crear servicios
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        // Super admin puede editar todos los servicios
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Los usuarios solo pueden editar sus propios servicios
        return $service->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        // Super admin puede eliminar todos los servicios
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Los usuarios solo pueden eliminar sus propios servicios si no tienen citas asociadas
        if ($service->user_id !== $user->id) {
            return false;
        }

        // No permitir eliminar servicios que ya tienen citas
        return $service->appointments()->count() === 0;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return false;
    }
}
