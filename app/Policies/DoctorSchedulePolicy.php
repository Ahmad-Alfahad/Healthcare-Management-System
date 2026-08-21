<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;

class DoctorSchedulePolicy
{

    public function viewAny(User $user): bool
    {
        return true;
    }


    public function view(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return true;
    }


    public function create(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin()
            || $user->managesFacility(
                $doctor
                    ->facilityDepartmentSpecialization
                    ->facilityDepartment
                    ->facility
            );
    }


    public function update(
        User $user,
        DoctorSchedule $doctorSchedule
    ): bool {
        return $user->isAdmin()
            || $user->managesFacility(
                $doctorSchedule
                    ->doctor
                    ->facilityDepartmentSpecialization
                    ->facilityDepartment
                    ->facility
            );
    }


    public function delete(
        User $user,
        DoctorSchedule $doctorSchedule
    ): bool {
        return $user->isAdmin()
            || $user->managesFacility(
                $doctorSchedule
                    ->doctor
                    ->facilityDepartmentSpecialization
                    ->facilityDepartment
                    ->facility
            );
    }
}