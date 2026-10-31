<?php

namespace App\Policies;

use App\Models\Ecue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EcuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ecues.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ecue $ecue): bool
    {
        return $user->can('ecues.view');    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('ecues.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ecue $ecue): bool
    {
        return $user->can('ecues.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ecue $ecue): bool
    {
        return $user->can('ecues.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ecue $ecue): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ecue $ecue): bool
    {
        return false;
    }
}
