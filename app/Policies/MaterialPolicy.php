<?php

namespace App\Policies;

use App\Models\User;

class MaterialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->can('view-any-material'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return ($user->can('view-material'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->can('create-material'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return ($user->can('update-material'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return ($user->can('delete-material'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return ($user->can('restore-material'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return ($user->can('force-delete-material'));
    }
}