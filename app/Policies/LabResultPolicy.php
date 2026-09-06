<?php

namespace App\Policies;

use App\Models\LabRequestItem;
use App\Models\LabResult;
use App\Models\LabStaff;
use App\Models\User;

class LabResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient()
            || $user->isLabStaff();
    }

    public function view(User $user, LabResult $labResult): bool
    {
        return $this->canAccessRequest($user, $labResult->labRequestItem);
    }

    public function create(
        User $user,
        LabRequestItem $labRequestItem,
    ): bool {
        if ($user->isAdmin()) {
            return true;
        }

        $labStaff = $user->labStaff;

        return $user->isLabStaff()
            && $labStaff !== null
            && $this->staffCanAccessRequest($labStaff, $labRequestItem);
    }

    public function update(User $user, LabResult $labResult): bool
    {
        return $this->canManageResult($user, $labResult);
    }

    public function delete(User $user, LabResult $labResult): bool
    {
        return $this->canManageResult($user, $labResult);
    }

    private function canManageResult(User $user, LabResult $labResult): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isLabStaff()
            && $user->labStaff?->id === $labResult->lab_staff_id
            && $this->staffCanAccessRequest(
                $user->labStaff,
                $labResult->labRequestItem
            );
    }

    private function canAccessRequest(User $user, ?LabRequestItem $labRequestItem): bool
    {
        if ($labRequestItem === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $visit = $labRequestItem->visit;

        if ($visit === null) {
            return false;
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
            && in_array($facility->id, $user->accessibleFacilityIds(), true);
    }

    private function staffCanAccessRequest(
        LabStaff $labStaff,
        LabRequestItem $labRequestItem
    ): bool {
        $facility = $this->visitFacility($labRequestItem->visit);

        return $facility !== null
            && in_array($facility->id, $labStaff->employee?->facility?->familyIds() ?? [], true);
    }

    private function visitFacility($visit)
    {
        return $visit?->appointment
            ?->doctor
            ?->facilityDepartmentSpecialization
            ?->facilityDepartment
            ?->facility;
    }
}
