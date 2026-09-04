<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;

class DoctorSchedulePolicy
{

    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient();
    }

    public function view(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isPatient()
            || ($user->isDoctor() && $user->doctor?->id === $doctorSchedule->doctor_id);
    }


    public function create(User $user, Doctor $doctor): bool
    {
        return $user->isAdmin()
            || $user->managesFacility(
                $doctor
                    ->facilityDepartmentSpecialization
                    ->facilityDepartment
                    ->facility
            ) || ($user->isDoctor() && $user->doctor?->id === $doctor->id);
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
            )|| ($user->isDoctor() && $user->doctor?->id === $doctorSchedule->doctor_id);
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
            ) || ($user->isDoctor() && $user->doctor?->id === $doctorSchedule->doctor_id);
    }
}
