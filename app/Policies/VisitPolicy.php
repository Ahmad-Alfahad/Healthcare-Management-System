<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Visit;

class VisitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient();
    }

    public function view(User $user, Visit $visit): bool
    {
        return $this->canAccessVisit($user, $visit);
    }

    public function create(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $appointment->doctor_id;
        }

        if ($user->isManager()) {
            return $this->managerOwnsAppointment($user, $appointment);
        }

        return false;
    }

    public function update(User $user, Visit $visit): bool
    {
        return $this->canManageVisit($user, $visit);
    }

    public function delete(User $user, Visit $visit): bool
    {
        return $this->canManageVisit($user, $visit);
    }

    public function changeStatus(User $user, Visit $visit): bool
    {
        return $this->canManageVisit($user, $visit);
    }

    private function canAccessVisit(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $visit->doctor_id;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $visit->patient_id;
        }

        if ($user->isManager()) {
            return $this->managerOwnsAppointment($user, $visit->appointment);
        }

        return false;
    }

    private function canManageVisit(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $visit->doctor_id;
        }

        return $user->isManager()
            && $this->managerOwnsAppointment($user, $visit->appointment);
    }

    private function managerOwnsAppointment(User $user, Appointment $appointment): bool
    {
        $facility = $appointment->doctor
            ?->facilityDepartmentSpecialization
            ?->facilityDepartment
            ?->facility;

        return $facility !== null
            && $user->managesFacility($facility);
    }
}
