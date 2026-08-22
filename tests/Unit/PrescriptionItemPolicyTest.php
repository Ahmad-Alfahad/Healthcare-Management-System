<?php

namespace Tests\Unit;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\Visit;
use App\Policies\PrescriptionItemPolicy;
use Tests\TestCase;

class PrescriptionItemPolicyUser extends User
{
    public string $policyRole = '';

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
}

class PrescriptionItemPolicyTest extends TestCase
{
    public function test_doctor_can_create_an_item_for_their_prescription(): void
    {
        $user = $this->userWithRole('doctor');
        $user->setRelation('doctor', Doctor::make()->forceFill(['id' => 7]));
        $prescription = $this->prescriptionForDoctor(7);

        self::assertTrue((new PrescriptionItemPolicy())->create($user, $prescription));
    }

    public function test_patient_can_view_an_item_from_their_prescription(): void
    {
        $user = $this->userWithRole('patient');
        $user->setRelation('patient', Patient::make()->forceFill(['id' => 11]));
        $item = $this->itemForPatient(11);

        self::assertTrue((new PrescriptionItemPolicy())->view($user, $item));
    }

    public function test_doctor_cannot_update_an_item_from_another_doctors_prescription(): void
    {
        $user = $this->userWithRole('doctor');
        $user->setRelation('doctor', Doctor::make()->forceFill(['id' => 7]));
        $item = $this->itemForDoctor(8);

        self::assertFalse((new PrescriptionItemPolicy())->update($user, $item));
    }

    public function test_patient_cannot_delete_an_item(): void
    {
        $user = $this->userWithRole('patient');
        $user->setRelation('patient', Patient::make()->forceFill(['id' => 11]));
        $item = $this->itemForPatient(11);

        self::assertFalse((new PrescriptionItemPolicy())->delete($user, $item));
    }

    private function userWithRole(string $role): User
    {
        $user = new PrescriptionItemPolicyUser();
        $user->policyRole = $role;

        return $user;
    }

    private function prescriptionForDoctor(int $doctorId): Prescription
    {
        $visit = Visit::make(['doctor_id' => $doctorId]);

        return Prescription::make()->setRelation('visit', $visit);
    }

    private function itemForDoctor(int $doctorId): PrescriptionItem
    {
        return PrescriptionItem::make()->setRelation(
            'prescription',
            $this->prescriptionForDoctor($doctorId)
        );
    }

    private function itemForPatient(int $patientId): PrescriptionItem
    {
        $visit = Visit::make(['patient_id' => $patientId]);
        $prescription = Prescription::make()->setRelation('visit', $visit);

        return PrescriptionItem::make()->setRelation('prescription', $prescription);
    }
}
