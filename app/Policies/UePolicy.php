<?php

namespace App\Policies;

use App\Models\Ue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
         return $user->can('ues.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ue $ue): bool
    {
          return $user->can('ues.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
          return $user->can('ues.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ue $ue): bool
    {
          return $user->can('ues.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ue $ue): bool
    {
          return $user->can('ues.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ue $ue): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ue $ue): bool
    {
        return false;
    }
}
