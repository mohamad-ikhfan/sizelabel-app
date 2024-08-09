<?php

namespace App\Policies;

use App\Models\User;

class DestroyRibbonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->can('view-any-destroy-ribbon'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return ($user->can('view-destroy-ribbon'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->can('create-destroy-ribbon'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ($user->can('update-destroy-ribbon'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ($user->can('delete-destroy-ribbon'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ($user->can('restore-destroy-ribbon'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ($user->can('force-delete-destroy-ribbon'));
    }
}