<?php

namespace App\Services;

use App\Repositories\PharmacistRepository;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\Pharmacist;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class PharmacistService
{
    protected PharmacistRepository $pharmacistRepository;

    public function __construct(PharmacistRepository $pharmacistRepository)
    {
        $this->pharmacistRepository = $pharmacistRepository;
    }

    public function getAllPharmacists(User $user, array $filters = [])
    {
        if ($user->isAdmin()) {
            return $this->pharmacistRepository->all($filters);
        }

        if ($user->isManager()) {
            $facility = $user->facility();

            if (!$facility) {
                return new Collection();
            }

            return $this->pharmacistRepository->getByFacility(
                $user->accessibleFacilityIds(),
                $filters
            );
        }
        return $this->pharmacistRepository->all($filters);
    }

    public function getPharmacistById(int $id): Pharmacist
    {
        return $this->pharmacistRepository->find($id);
    }

    public function createPharmacist(array $data): Pharmacist
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

        return $this->pharmacistRepository->create(
            array_diff_key($data, array_flip(['is_active']))
        );
    }

    public function updatePharmacist(int $id, array $data): bool
    {
        $pharmacist = $this->pharmacistRepository->find($id);
        $employee = $pharmacist->employee;

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

        return $this->pharmacistRepository->update(
            $id,
            array_diff_key($data, array_flip(['is_active']))
        );
    }

    public function deletePharmacist(int $id): bool
    {
        return $this->pharmacistRepository->delete($id);
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
        $query = Pharmacist::where(
            'employee_id',
            $employeeId
        );

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Employee is already assigned to another pharmacist.'
                ]
            ]);
        }
    }

    private function validateFacilityType(
        Facility $facility
    ): void {
        if ($facility->facility_type !== 'pharmacy') {
            throw ValidationException::withMessages([
                'employee_id' => [
                    'Pharmacist must belong to a pharmacy.'
                ]
            ]);
        }
    }
}
