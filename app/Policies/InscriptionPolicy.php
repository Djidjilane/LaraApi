<?php

namespace App\Policies;

use App\Models\Inscription;
use App\Models\Inscriptions;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InscriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('inscriptions.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inscriptions $inscription): bool
    {
        return $user->can('inscriptions.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('inscriptions.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Inscriptions $inscription): bool
    {
        return $user->can('inscriptions.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Inscriptions $inscription): bool
    {
        return $user->can('inscriptions.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Inscriptions $inscription): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Inscriptions $inscription): bool
    {
        return false;
    }
}
