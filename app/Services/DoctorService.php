<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use App\Models\Doctor;
use App\Models\FacilityDepartmentSpecialization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DoctorService
{
    protected DoctorRepository $doctorRepository;

    public function __construct(DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    public function getAllDoctors(): Collection
    {
        return $this->doctorRepository->all();
    }

    public function getDoctorById(int $id): Doctor
    {
        return $this->doctorRepository->find($id);
    }

    public function createDoctor(array $data): Doctor
    {
        $this->validateProfileIsNotAssigned($data['profile_id']);
        $this->validateFacilityDepartmentSpecialization(
            $data['facility_department_specialization_id']
        );


        return $this->doctorRepository->create($data);
    }

    public function updateDoctor(int $id, array $data): bool
    {
        if (isset($data['facility_department_specialization_id'])) {
            $this->validateFacilityDepartmentSpecialization(
                $data['facility_department_specialization_id']
            );
        }

        return $this->doctorRepository->update($id, $data);
    }

    public function deleteDoctor(int $id): bool
    {
        $doctor = Doctor::withCount([
            'appointments',
            'doctorSchedule',
            'visits'
        ])->findOrFail($id);

        if ($this->hasLinkedRecords($doctor)) {
            throw ValidationException::withMessages([
                'doctor' => [
                    'Cannot delete doctor because the doctor has linked records. Deactivate the doctor instead.'
                ]
            ]);
        }

        return $this->doctorRepository->delete($id);
    }

    private function hasLinkedRecords(Doctor $doctor): bool
    {
        return $doctor->appointments_count > 0
            || $doctor->doctor_schedule_count > 0
            || $doctor->visits_count > 0;
    }

    private function validateProfileIsNotAssigned(int $profileId): void
    {
        if (
            Doctor::where('profile_id', $profileId)
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'profile_id' => [
                    'This profile is already assigned to another doctor.'
                ]
            ]);
        }
    }

    private function validateFacilityDepartmentSpecialization(int $facilityDepartmentSpecializationId): void
    {
        $facilityDepartmentSpecialization =
            FacilityDepartmentSpecialization::with([
                'specialization',
                'facilityDepartment.facility',
                'facilityDepartment.department'
            ])->find($facilityDepartmentSpecializationId);

        if (!$facilityDepartmentSpecialization) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Facility department specialization does not exist.'
                ]
            ]);
        }

        if (!$facilityDepartmentSpecialization->is_active) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Facility department specialization is inactive.'
                ]
            ]);
        }

        if (
            !$facilityDepartmentSpecialization
                ->facilityDepartment
                ->department
                ->is_active
        ) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Department is inactive.'
                ]
            ]);
        }

        if (
            !$facilityDepartmentSpecialization
                ->specialization
                ->is_active
        ) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Specialization is inactive.'
                ]
            ]);
        }

        if (
            !$facilityDepartmentSpecialization
                ->facilityDepartment
                ->facility
                ->is_active
        ) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Facility is inactive.'
                ]
            ]);
        }
    }
}
