<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\FacilityDepartment;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\User;

class FacilityDepartmentSpecializationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    public function view(
        User $user,
        FacilityDepartmentSpecialization $assignment
    ): bool {
        return $user->isAdmin()
            || $user->managesFacility(
                $assignment->facilityDepartment->facility
            );
    }

    public function create(
        User $user,
        FacilityDepartment $facilityDepartment
    ): bool {
        return $user->isAdmin()
            || $user->managesFacility(
                $facilityDepartment->facility
            );
    }

    public function update(
        User $user,
        FacilityDepartmentSpecialization $assignment
    ): bool {
        return $user->isAdmin()
            || $user->managesFacility(
                $assignment->facilityDepartment->facility
            );
    }

    public function delete(
        User $user,
        FacilityDepartmentSpecialization $assignment
    ): bool {
        return $user->isAdmin()
            || $user->managesFacility(
                $assignment->facilityDepartment->facility
            );
    }
}