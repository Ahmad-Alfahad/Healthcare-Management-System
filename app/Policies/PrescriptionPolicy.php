<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;

class PrescriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient()
            || $user->isPharmacist();
    }

    public function view(User $user, Prescription $prescription): bool
    {
        return $this->canAccessVisit($user, $prescription->visit);
    }

    public function create(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDoctor()
            && $user->doctor?->id === $visit->doctor_id;
    }

    public function update(User $user, Prescription $prescription): bool
    {
        return $this->canManageVisit($user, $prescription->visit);
    }

    public function delete(User $user, Prescription $prescription): bool
    {
        return $this->canManageVisit($user, $prescription->visit);
    }

    public function cancel(User $user, Prescription $prescription): bool
    {
        return $this->canManageVisit($user, $prescription->visit);
    }

    public function dispense(User $user, Prescription $prescription): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isPharmacist()
            && $this->userCanAccessVisitFacility($user, $prescription->visit);
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
            return $this->managerOwnsVisit($user, $visit);
        }

        return $user->isPharmacist()
            && $this->userCanAccessVisitFacility($user, $visit);
    }

    private function canManageVisit(User $user, Visit $visit): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDoctor()
            && $user->doctor?->id === $visit->doctor_id;
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

    private function userCanAccessVisitFacility(User $user, Visit $visit): bool
    {
        $facility = $visit->appointment
            ?->doctor
            ?->facilityDepartmentSpecialization
            ?->facilityDepartment
            ?->facility;

        return $facility !== null
            && in_array($facility->id, $user->accessibleFacilityIds(), true);
    }
}
