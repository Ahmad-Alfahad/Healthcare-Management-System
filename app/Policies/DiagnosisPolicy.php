<?php

namespace App\Policies;

use App\Models\Diagnosis;
use App\Models\User;
use App\Models\Visit;

class DiagnosisPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient();
    }

    public function view(User $user, Diagnosis $diagnosis): bool
    {
        return $this->canAccessVisit($user, $diagnosis->visit);
    }

    public function create(User $user, Visit $visit): bool
    {
        return $this->canManageVisit($user, $visit);
    }

    public function update(User $user, Diagnosis $diagnosis): bool
    {
        return $this->canManageVisit($user, $diagnosis->visit);
    }

    public function delete(User $user, Diagnosis $diagnosis): bool
    {
        return $this->canManageVisit($user, $diagnosis->visit);
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

        return $user->isManager() && $this->managerOwnsVisit($user, $visit);
    }

    private function canManageVisit(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $visit->doctor_id;
        }

        return $user->isManager() && $this->managerOwnsVisit($user, $visit);
    }

    private function managerOwnsVisit(User $user, Visit $visit): bool
    {
        $facility = $visit->appointment
            ?->doctor
            ?->facilityDepartmentSpecialization
            ?->facilityDepartment
            ?->facility;

        return $facility !== null
            && $user->managesFacility($facility);
    }
}
