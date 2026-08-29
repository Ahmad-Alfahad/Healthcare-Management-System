<?php

namespace App\Policies;

use App\Models\Dispensing;
use App\Models\Pharmacist;
use App\Models\PrescriptionItem;
use App\Models\User;

class DispensingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isManager()
            || $user->isDoctor()
            || $user->isPatient()
            || $user->isPharmacist();
    }

    public function view(User $user, Dispensing $dispensing): bool
    {
        return $this->canAccessItem($user, $dispensing->prescriptionItem);
    }

    public function create(
        User $user,
        PrescriptionItem $item,
        ?Pharmacist $pharmacist = null
    ): bool {
        if ($user->isAdmin()) {
            return true;
        }

        // إذا لم يُمرر $pharmacist نستخرج القيمة المربوطة بالمستخدم مباشرة
        $currentPharmacist = $pharmacist ?? $user->pharmacist;

        return $user->isPharmacist()
            && $currentPharmacist !== null
            && $currentPharmacist->is_active !== false
            && $user->pharmacist?->id === $currentPharmacist->id
            && $this->pharmacistCanAccessItem($currentPharmacist, $item);
    }

    public function update(User $user, Dispensing $dispensing): bool
    {
        return $this->canManageDispensing($user, $dispensing);
    }

    public function delete(User $user, Dispensing $dispensing): bool
    {
        return $this->canManageDispensing($user, $dispensing);
    }

    private function canManageDispensing(User $user, Dispensing $dispensing): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isPharmacist()
            && $user->pharmacist?->id === $dispensing->pharmacist_id
            && $this->pharmacistCanAccessItem(
                $user->pharmacist,
                $dispensing->prescriptionItem
            );
    }

    private function canAccessItem(User $user, ?PrescriptionItem $item): bool
    {
        if ($item === null) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $prescription = $item->prescription;
        if ($prescription === null) {
            return false;
        }

        $visit = $prescription->visit;
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

        return $user->isPharmacist()
            && $facility !== null
            && in_array($facility->id, $user->accessibleFacilityIds(), true);
    }

    private function pharmacistCanAccessItem(
        Pharmacist $pharmacist,
        PrescriptionItem $item
    ): bool {
        $prescription = $item->prescription;
        if ($prescription === null || $prescription->visit === null) {
            return false;
        }

        $facility = $this->visitFacility($prescription->visit);

        return $facility !== null && $pharmacist->facility_id === $facility->id;
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