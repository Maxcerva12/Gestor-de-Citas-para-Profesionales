<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MedicalHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalHistoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_medical::history');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->can('view_medical::history');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_medical::history');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->can('update_medical::history');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->can('delete_medical::history');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_medical::history');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->can('force_delete_medical::history');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_medical::history');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->can('restore_medical::history');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_medical::history');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->can('replicate_medical::history');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_medical::history');
    }
}
