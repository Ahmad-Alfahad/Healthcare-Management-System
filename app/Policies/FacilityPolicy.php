<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;
use App\Models\FacilityDepartment;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Facility $facility): bool
    {
        return true;
    }

    public function viewStaff(User $user, Facility $facility): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($facility));
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->isAdmin()
            || $user->managesFacility($facility);
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->isAdmin();
    }

    public function attachDepartment(User $user, Facility $facility): bool
    {
        if ($user->isAdmin() || $user->managesFacility($facility)) {
            return true;
        }

        return false;
    }

    public function detachDepartment(User $user,  Facility $facility): bool
    {
        return $user->isAdmin()
            || $user->managesFacility($facility);
    }
}
