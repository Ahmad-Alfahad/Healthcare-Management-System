<?php

namespace Tests\Unit;

use App\Models\Doctor;
use App\Models\Facility;
use App\Models\FacilityDepartment;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Policies\PrescriptionPolicy;
use Tests\TestCase;

class PrescriptionPolicyUser extends User
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

class PrescriptionPolicyTest extends TestCase
{
    public function test_doctor_can_create_a_prescription_for_their_visit(): void
    {
        $user = $this->userWithRole('doctor');
        $doctor = Doctor::make()->forceFill(['id' => 7]);
        $user->setRelation('doctor', $doctor);
        $visit = Visit::make(['doctor_id' => 7]);

        self::assertTrue((new PrescriptionPolicy())->create($user, $visit));
    }

    public function test_doctor_cannot_manage_another_doctors_prescription(): void
    {
        $user = $this->userWithRole('doctor');
        $doctor = Doctor::make()->forceFill(['id' => 7]);
        $user->setRelation('doctor', $doctor);
        $visit = Visit::make(['doctor_id' => 8]);
        $prescription = Prescription::make()->setRelation('visit', $visit);

        self::assertFalse((new PrescriptionPolicy())->update($user, $prescription));
    }

    public function test_patient_can_view_only_their_own_prescription(): void
    {
        $user = $this->userWithRole('patient');
        $patient = Patient::make()->forceFill(['id' => 11]);
        $user->setRelation('patient', $patient);
        $visit = Visit::make(['patient_id' => 11]);
        $prescription = Prescription::make()->setRelation('visit', $visit);

        self::assertTrue((new PrescriptionPolicy())->view($user, $prescription));
    }

    public function test_pharmacist_cannot_view_a_prescription_from_another_facility(): void
    {
        $user = $this->userWithRole('pharmacist');
        $pharmacist = \App\Models\Pharmacist::make();
        $pharmacist->setRelation('facility', Facility::make()->forceFill(['id' => 1]));
        $user->setRelation('pharmacist', $pharmacist);
        $visit = Visit::make(['patient_id' => 11]);
        $visit->setRelation('appointment', $this->appointmentForFacility(2));
        $prescription = Prescription::make()->setRelation('visit', $visit);

        self::assertFalse((new PrescriptionPolicy())->view($user, $prescription));
    }

    private function userWithRole(string $role): User
    {
        $user = new PrescriptionPolicyUser();
        $user->policyRole = $role;

        return $user;
    }

    private function appointmentForFacility(int $facilityId): object
    {
        $facility = Facility::make()->forceFill(['id' => $facilityId]);
        $doctor = Doctor::make();
        $specialization = FacilityDepartmentSpecialization::make();
        $department = FacilityDepartment::make();
        $doctor->setRelation('facilityDepartmentSpecialization', $specialization);
        $specialization->setRelation('facilityDepartment', $department);
        $department->setRelation('facility', $facility);

        $appointment = new \App\Models\Appointment();
        $appointment->setRelation('doctor', $doctor);

        return $appointment;
    }
}
