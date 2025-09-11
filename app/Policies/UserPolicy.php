<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        // Si es un User (admin/professional)
        if ($user instanceof User) {
            return $user->can('view_any_user');
        }

        // Si es un Client, puede ver la lista de profesionales
        if ($user instanceof Client) {
            return true; // Los clientes pueden ver profesionales para agendar citas
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, User $targetUser = null): bool
    {
        // Si es un User (admin/professional)
        if ($user instanceof User) {
            return $user->can('view_user');
        }

        // Si es un Client, puede ver detalles de profesionales
        if ($user instanceof Client) {
            return true; // Los clientes pueden ver detalles de profesionales
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): bool
    {
        // Solo los Users (admin/professional) pueden crear usuarios
        if ($user instanceof User) {
            return $user->can('create_user');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, User $targetUser = null): bool
    {
        // Solo los Users (admin/professional) pueden actualizar usuarios
        if ($user instanceof User) {
            return $user->can('update_user');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, User $targetUser = null): bool
    {
        // Solo los Users (admin/professional) pueden eliminar usuarios
        if ($user instanceof User) {
            return $user->can('delete_user');
        }

        return false;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(Authenticatable $user): bool
    {
        // Solo los Users (admin/professional) pueden bulk delete
        if ($user instanceof User) {
            return $user->can('delete_any_user');
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(Authenticatable $user, User $targetUser = null): bool
    {
        // Solo los Users (admin/professional) pueden force delete
        if ($user instanceof User) {
            return $user->can('force_delete_user');
        }

        return false;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(Authenticatable $user): bool
    {
        // Solo los Users (admin/professional) pueden force delete any
        if ($user instanceof User) {
            return $user->can('force_delete_any_user');
        }

        return false;
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(Authenticatable $user, User $targetUser = null): bool
    {
        // Solo los Users (admin/professional) pueden restaurar
        if ($user instanceof User) {
            return $user->can('restore_user');
        }

        return false;
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(Authenticatable $user): bool
    {
        // Solo los Users (admin/professional) pueden bulk restore
        if ($user instanceof User) {
            return $user->can('restore_any_user');
        }

        return false;
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(Authenticatable $user, User $targetUser = null): bool
    {
        // Solo los Users (admin/professional) pueden replicar
        if ($user instanceof User) {
            return $user->can('replicate_user');
        }

        return false;
    }

    /**
     * Determine whether the user can reorder.
     */
    /**
     * Determine whether the user can reorder.
     */
    public function reorder(Authenticatable $user): bool
    {
        // Solo los Users (admin/professional) pueden reordenar
        if ($user instanceof User) {
            return $user->can('reorder_user');
        }

        return false;
    }
}
