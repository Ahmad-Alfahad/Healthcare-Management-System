<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Employee;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\User;
use App\Repositories\DoctorRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DoctorService
{
    protected DoctorRepository $doctorRepository;

    public function __construct(DoctorRepository $doctorRepository)
    {
        $this->doctorRepository = $doctorRepository;
    }

    public function getAllDoctors(User $user, array $filters = [])
    {
        if ($user->isAdmin()) {
            return $this->doctorRepository->all($filters);
        }

        if ($user->isManager()) {
            $facility = $user->facility();

            if (!$facility) {
                return new Collection();
            }

            return $this->doctorRepository->getByFacility(
                $user->accessibleFacilityIds(),
                $filters
            );
        }

        return $this->doctorRepository->all($filters);
    }

    public function getDoctorById(int $id): Doctor
    {
        return $this->doctorRepository->find($id);
    }

    public function createDoctor(array $data): Doctor
    {
        $employee = Employee::findOrFail($data['employee_id']);

        $this->validateEmployee($employee);

        $this->validateEmployeeIsNotAssigned(
            $data['employee_id']
        );

        $this->validateFacilityDepartmentSpecialization(
            $data['facility_department_specialization_id'],
            $employee->facility_id
        );

        $employee->update(array_intersect_key($data, array_flip(['languages', 'is_active'])));

        return $this->doctorRepository->create(
            array_diff_key($data, array_flip(['languages', 'is_active']))
        );
    }

    public function updateDoctor(int $id, array $data): bool
    {
        $doctor = $this->doctorRepository->find($id);

        $employee = $doctor->employee;

        $employeeData = array_intersect_key($data, array_flip(['languages', 'is_active']));
        if ($employeeData !== []) {
            $employee->update($employeeData);
        }

        if (isset($data['employee_id'])) {
            $employee = Employee::findOrFail($data['employee_id']);

            $this->validateEmployee($employee);

            $this->validateEmployeeIsNotAssigned(
                $data['employee_id'],
                $doctor->id
            );
        }

        if (isset($data['facility_department_specialization_id'])) {
            $this->validateFacilityDepartmentSpecialization(
                $data['facility_department_specialization_id'],
                $employee->facility_id
            );
        }

        return $this->doctorRepository->update(
            $id,
            array_diff_key($data, array_flip(['languages', 'is_active']))
        );
    }

    public function deleteDoctor(int $id): bool
    {
        $doctor = Doctor::withCount([
            'appointments',
            'doctorSchedule',
            'visits',
        ])->findOrFail($id);

        if ($this->hasLinkedRecords($doctor)) {
            throw ValidationException::withMessages([
                'doctor' => [
                    'Cannot delete doctor because the doctor has linked records. Deactivate the employee instead.',
                ],
            ]);
        }

        return $this->doctorRepository->delete($id);
    }

    private function validateEmployee(Employee $employee): void
    {
        if (!$employee->is_active) {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Employee is inactive.',
                ],
            ]);
        }
    }

    private function validateEmployeeIsNotAssigned(
        int $employeeId,
        ?int $exceptDoctorId = null
    ): void {
        $query = Doctor::where('employee_id', $employeeId);

        if ($exceptDoctorId !== null) {
            $query->where('id', '!=', $exceptDoctorId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'This employee is already assigned to another doctor.',
                ],
            ]);
        }
    }

    private function validateFacilityDepartmentSpecialization(
        int $facilityDepartmentSpecializationId,
        int $employeeFacilityId
    ): void {
        $assignment = FacilityDepartmentSpecialization::with([
            'specialization',
            'facilityDepartment.facility',
            'facilityDepartment.department',
        ])->find($facilityDepartmentSpecializationId);

        if (!$assignment) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Facility department specialization does not exist.',
                ],
            ]);
        }

        if (!$assignment->is_active) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Facility department specialization is inactive.',
                ],
            ]);
        }

        if (
            $assignment->facilityDepartment->facility_id
            !== $employeeFacilityId
        ) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'The selected facility department specialization does not belong to the employee facility.',
                ],
            ]);
        }

        if (
            !$assignment->facilityDepartment->facility->is_active
        ) {
            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Facility is inactive.',
                ],
            ]);
        }
    }

    private function hasLinkedRecords(Doctor $doctor): bool
    {
        return $doctor->appointments_count > 0
            || $doctor->doctor_schedule_count > 0
            || $doctor->visits_count > 0;
    }
}