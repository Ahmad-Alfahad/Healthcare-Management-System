<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\User;

class DoctorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Doctor $doctor): bool
    {
        return true;
    }

    public function create(
        User $user,
        FacilityDepartmentSpecialization $facilityDepartmentSpecialization
    ): bool {
        return $user->isAdmin()
            || $user->managesFacility(
                $facilityDepartmentSpecialization
                    ->facilityDepartment
                    ->facility
            );
    }

    public function update(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin()
            || $user->managesFacility(
                $doctor
                    ->facilityDepartmentSpecialization
                    ->facilityDepartment
                    ->facility
            );
    }

    public function delete(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin()
            || $user->managesFacility(
                $doctor
                    ->facilityDepartmentSpecialization
                    ->facilityDepartment
                    ->facility
            );
    }
}