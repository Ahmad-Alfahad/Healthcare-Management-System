<?php

namespace App\Policies;

use App\Models\LabRequestItem;
use App\Models\User;
use App\Models\Visit;

class LabRequestItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient()
            || $user->isLabStaff();
    }

    public function view(User $user, LabRequestItem $labRequestItem): bool
    {
        return $this->canAccessVisit($user, $labRequestItem->visit);
    }

    public function create(User $user, Visit $visit): bool
    {
        return $this->canManageVisit($user, $visit);
    }

    public function update(User $user, LabRequestItem $labRequestItem): bool
    {
        return $this->canManageVisit($user, $labRequestItem->visit);
    }

    public function delete(User $user, LabRequestItem $labRequestItem): bool
    {
        return $this->canManageVisit($user, $labRequestItem->visit);
    }

    private function canAccessVisit(User $user, ?Visit $visit): bool
    {
        if ($visit === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $visit->doctor_id;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $visit->patient_id;
        }

        $facility = $this->visitFacility($visit);

        if ($user->isManager()) {
            return $facility !== null && $user->managesFacility($facility);
        }

        return $user->isLabStaff()
            && $facility !== null
            && $user->facility()?->id === $facility->id;
    }

    private function canManageVisit(User $user, ?Visit $visit): bool
    {
        if ($visit === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $user->doctor?->id === $visit->doctor_id;
        }

        $facility = $this->visitFacility($visit);

        return $user->isManager()
            && $facility !== null
            && $user->managesFacility($facility);
    }

    private function visitFacility(Visit $visit)
    {
        return $visit->appointment
            ?->doctor
            ?->facilityDepartmentSpecialization
            ?->facilityDepartment
            ?->facility;
    }
}
