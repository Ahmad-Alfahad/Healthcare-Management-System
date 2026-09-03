<?php

namespace App\Policies;

use App\Models\Pharmacist;
use App\Models\Facility;
use App\Models\User;

class PharmacistPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pharmacist $pharmacist): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($pharmacist->employee->facility))
            || $user->id === $pharmacist->employee->profile->user_id;
    }

    public function create(User $user, Facility $facility): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($facility));
    }

    public function update(User $user, Pharmacist $pharmacist): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($pharmacist->employee->facility));
    }

    public function delete(User $user, Pharmacist $pharmacist): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($pharmacist->employee->facility));
    }
}