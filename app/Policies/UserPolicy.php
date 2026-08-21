<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }


    public function view(User $user, User $target): bool
    {
        // System administrator can view any user.
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $target->id;
    }


    public function create(User $user): bool
    {
        return $user->isManagement();
    }


    public function update(User $user, User $target): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->isAdmin();
    }

    public function managePermissions(User $user, User $target): bool
    {
        return $user->isAdmin();
    }
}
