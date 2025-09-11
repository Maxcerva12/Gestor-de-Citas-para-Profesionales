<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class AppointmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        // Si es un User (admin/professional)
        if ($user instanceof User) {
            return $user->can('view_any_client::appointment');
        }

        // Si es un Client
        if ($user instanceof Client) {
            return true; // Los clientes pueden ver sus propias citas
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, Appointment $appointment): bool
    {
        // Si es un User (admin/professional)
        if ($user instanceof User) {
            return $user->can('view_client::appointment');
        }

        // Si es un Client, solo puede ver sus propias citas
        if ($user instanceof Client) {
            return $appointment->client_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): bool
    {
        // Si es un User (admin/professional)
        if ($user instanceof User) {
            return $user->can('create_client::appointment');
        }

        // Si es un Client
        if ($user instanceof Client) {
            return true; // Los clientes pueden crear citas para sí mismos
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Appointment $appointment): bool
    {
        // Si es un User (admin/professional)
        if ($user instanceof User) {
            return $user->can('update_client::appointment');
        }

        // Si es un Client, solo puede actualizar sus propias citas y en ciertos estados
        if ($user instanceof Client) {
            return $appointment->client_id === $user->id &&
                in_array($appointment->status, ['pending', 'scheduled']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, Appointment $appointment): bool
    {
        // Si es un User (admin/professional)
        if ($user instanceof User) {
            return $user->can('delete_client::appointment');
        }

        // Si es un Client, solo puede cancelar sus propias citas pendientes
        if ($user instanceof Client) {
            return $appointment->client_id === $user->id &&
                $appointment->status === 'pending';
        }

        return false;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(Authenticatable $user): bool
    {
        // Solo los Users (admin/professional) pueden hacer bulk delete
        if ($user instanceof User) {
            return $user->can('delete_any_client::appointment');
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(Authenticatable $user, Appointment $appointment): bool
    {
        // Solo los Users (admin/professional) pueden force delete
        if ($user instanceof User) {
            return $user->can('force_delete_client::appointment');
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
            return $user->can('force_delete_any_client::appointment');
        }

        return false;
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(Authenticatable $user, Appointment $appointment): bool
    {
        // Solo los Users (admin/professional) pueden restaurar
        if ($user instanceof User) {
            return $user->can('restore_client::appointment');
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
            return $user->can('restore_any_client::appointment');
        }

        return false;
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(Authenticatable $user, Appointment $appointment): bool
    {
        // Solo los Users (admin/professional) pueden replicar
        if ($user instanceof User) {
            return $user->can('replicate_client::appointment');
        }

        return false;
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(Authenticatable $user): bool
    {
        // Solo los Users (admin/professional) pueden reordenar
        if ($user instanceof User) {
            return $user->can('reorder_client::appointment');
        }

        return false;
    }
}
