<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Employee::with(['profile.user.roles', 'facility']),
            $filters,
            ['languages'],
            [
                'profile' => ['full_name', 'phone', 'national_number'],
                'facility' => ['name'],
            ],
            ['facility_id' => 'facility_id']
        );
    }

    public function getByFacility(array $facilityIds, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Employee::with(['profile.user.roles', 'facility'])
                ->whereIn('facility_id', $facilityIds),
            $filters,
            ['languages'],
            [
                'profile' => ['full_name', 'phone', 'national_number'],
                'facility' => ['name'],
            ]
        );
    }

    public function find(int $id): Employee
    {
        return Employee::with([
            'profile',
            'facility',
            'doctor',
            'pharmacist',
            'labStaff',
        ])->findOrFail($id);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $employee = Employee::findOrFail($id);

        return $employee->update($data);
    }

    public function delete(int $id): bool
    {
        $employee = Employee::findOrFail($id);

        return $employee->delete();
    }
}
