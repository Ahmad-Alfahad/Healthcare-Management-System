<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;

class ProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    public function view(User $user, Profile $profile): bool
    {
        if ($user->isManagement()) {
            return true;
        }

        return $profile->belongsToUser($user);
    }

    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, Profile $profile): bool
    {
        if ($user->isManagement()) {
            return true;
        }

        return $profile->belongsToUser($user);
    }

    public function delete(User $user, Profile $profile): bool
    {
        return false;
    }
}