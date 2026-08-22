<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Dispensing;
use App\Models\Facility;
use App\Models\FacilityDepartment;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\Pharmacist;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\Visit;
use App\Policies\DispensingPolicy;
use Tests\TestCase;

class DispensingPolicyUser extends User
{
    public string $policyRole = '';
    public ?Facility $policyFacility = null;

    public function isAdmin(): bool
    {
        return $this->policyRole === 'admin';
    }

    public function isManager(): bool
    {
        return $this->policyRole === 'manager';
    }

    public function isDoctor(): bool
    {
        return $this->policyRole === 'doctor';
    }

    public function isPatient(): bool
    {
        return $this->policyRole === 'patient';
    }

    public function isPharmacist(): bool
    {
        return $this->policyRole === 'pharmacist';
    }

    public function facility(): ?Facility
    {
        return $this->policyFacility;
    }
}

class DispensingPolicyTest extends TestCase
{
    public function test_pharmacist_can_create_dispensing_for_their_facility(): void
    {
        $user = $this->userWithRole('pharmacist', 1);
        $user->setRelation('pharmacist', Pharmacist::make()->forceFill(['id' => 9]));
        $item = $this->itemForFacility(1);

        self::assertTrue((new DispensingPolicy())->create($user, $item, 9));
    }

    public function test_pharmacist_cannot_create_dispensing_for_another_facility(): void
    {
        $user = $this->userWithRole('pharmacist', 1);
        $user->setRelation('pharmacist', Pharmacist::make()->forceFill(['id' => 9]));
        $item = $this->itemForFacility(2);

        self::assertFalse((new DispensingPolicy())->create($user, $item, 9));
    }

    public function test_pharmacist_cannot_impersonate_another_pharmacist(): void
    {
        $user = $this->userWithRole('pharmacist', 1);
        $user->setRelation('pharmacist', Pharmacist::make()->forceFill(['id' => 9]));
        $item = $this->itemForFacility(1);

        self::assertFalse((new DispensingPolicy())->create($user, $item, 10));
    }

    public function test_only_admin_can_update_or_delete_dispensing_records(): void
    {
        $dispensing = Dispensing::make();
        $policy = new DispensingPolicy();

        self::assertTrue($policy->update($this->userWithRole('admin'), $dispensing));
        self::assertFalse($policy->update($this->userWithRole('pharmacist'), $dispensing));
        self::assertTrue($policy->delete($this->userWithRole('admin'), $dispensing));
        self::assertFalse($policy->delete($this->userWithRole('pharmacist'), $dispensing));
    }

    private function userWithRole(string $role, ?int $facilityId = null): User
    {
        $user = new DispensingPolicyUser();
        $user->policyRole = $role;
        $user->policyFacility = $facilityId === null
            ? null
            : Facility::make()->forceFill(['id' => $facilityId]);

        return $user;
    }

    private function itemForFacility(int $facilityId): PrescriptionItem
    {
        $facility = Facility::make()->forceFill(['id' => $facilityId]);
        $department = FacilityDepartment::make()->setRelation('facility', $facility);
        $specialization = FacilityDepartmentSpecialization::make()
            ->setRelation('facilityDepartment', $department);
        $doctor = Doctor::make()->setRelation(
            'facilityDepartmentSpecialization',
            $specialization
        );
        $appointment = Appointment::make()->setRelation('doctor', $doctor);
        $visit = Visit::make()->setRelation('appointment', $appointment);
        $prescription = Prescription::make()->setRelation('visit', $visit);

        return PrescriptionItem::make()->setRelation('prescription', $prescription);
    }
}
