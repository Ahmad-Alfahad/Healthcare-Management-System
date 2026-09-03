<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Facility;
use App\Models\LabStaff;
use App\Models\User;
use App\Repositories\LabStaffRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class LabStaffService
{
    protected LabStaffRepository $labStaffRepository;

    public function __construct(
        LabStaffRepository $labStaffRepository
    ) {
        $this->labStaffRepository = $labStaffRepository;
    }

    public function getAllStaff(
        User $user,
        array $filters = []
    ) {
        if ($user->isAdmin()) {
            return $this->labStaffRepository->all($filters);
        }

        if ($user->isManager()) {
            $facility = $user->facility();

            if (!$facility) {
                return new Collection();
            }

            return $this->labStaffRepository->getByFacility(
                $user->accessibleFacilityIds(),
                $filters
            );
        }

        return $this->labStaffRepository->all($filters);
    }

    public function getStaffById(int $id): LabStaff
    {
        return $this->labStaffRepository->find($id);
    }

    public function createStaff(array $data): LabStaff
    {
        $employee = Employee::findOrFail(
            $data['employee_id']
        );

        $this->validateEmployee($employee);

        $this->validateEmployeeNotAssigned(
            $data['employee_id']
        );

        $this->validateFacilityType(
            $employee->facility
        );

        $employee->update(array_intersect_key($data, array_flip(['is_active'])));

        return $this->labStaffRepository->create(
            array_diff_key($data, array_flip(['is_active']))
        );
    }

    public function updateStaff(
        int $id,
        array $data
    ): bool {
        $labStaff = $this->labStaffRepository->find($id);
        $employee = $labStaff->employee;

        if (isset($data['employee_id'])) {
            $employee = Employee::findOrFail(
                $data['employee_id']
            );

            $this->validateEmployee($employee);

            $this->validateEmployeeNotAssigned(
                $data['employee_id'],
                $id
            );

            $this->validateFacilityType(
                $employee->facility
            );
        }

        $employeeData = array_intersect_key($data, array_flip(['is_active']));
        if ($employeeData !== []) {
            $employee->update($employeeData);
        }

        return $this->labStaffRepository->update(
            $id,
            array_diff_key($data, array_flip(['is_active']))
        );
    }

    public function deleteStaff(int $id): bool
    {
        return $this->labStaffRepository->delete($id);
    }

    private function validateEmployee(
        Employee $employee
    ): void {
        if (!$employee->is_active) {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Employee is inactive.'
                ]
            ]);
        }

        if (!$employee->facility) {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Employee is not assigned to a facility.'
                ]
            ]);
        }

        if (!$employee->facility->is_active) {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Employee facility is inactive.'
                ]
            ]);
        }
    }

    private function validateEmployeeNotAssigned(
        int $employeeId,
        ?int $ignoreId = null
    ): void {
        $query = LabStaff::where(
            'employee_id',
            $employeeId
        );

        if ($ignoreId !== null) {
            $query->where(
                'id',
                '!=',
                $ignoreId
            );
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Employee is already assigned to another laboratory staff member.'
                ]
            ]);
        }
    }

    private function validateFacilityType(
        Facility $facility
    ): void {
        if ($facility->facility_type !== 'laboratory') {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Lab staff must belong to a laboratory.'
                ]
            ]);
        }
    }
}