<?php

namespace App\Policies;

use App\Models\Pharmacist;
use App\Models\User;

class PharmacistPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pharmacist $pharmacist): bool
    {
        return $user->isManagement() || $user->id === $pharmacist->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, Pharmacist $pharmacist): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, Pharmacist $pharmacist): bool
    {
        return false;
    }
}