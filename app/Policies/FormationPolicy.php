<?php

namespace App\Policies;

use App\Models\Formation;
use App\Models\User;

class FormationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('formations.view');
    }

    public function view(User $user, Formation $formation): bool
    {
        return $user->can('formations.view');
    }

    public function create(User $user): bool
    {
        return $user->can('formations.create');
    }

    public function update(User $user, Formation $formation): bool
    {
        return $user->can('formations.edit');
    }

    public function delete(User $user, Formation $formation): bool
    {
        return $user->can('formations.delete');
    }
}
