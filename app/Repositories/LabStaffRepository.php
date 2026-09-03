<?php

namespace App\Repositories;

use App\Models\LabStaff;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class LabStaffRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = LabStaff::with(['employee.profile', 'employee.facility']);
        $this->applyStatusFilter($query, $filters);

        return $this->paginateList(
            $query,
            $filters,
            ['license_number'],
            [
                'employee.profile' => ['full_name'],
                'employee.facility' => ['name'],
            ]
        );
    }

    public function getByFacility(array $facilityIds, array $filters = []): LengthAwarePaginator
    {
        $query = LabStaff::with(['employee.profile', 'employee.facility'])
            ->whereHas('employee', fn($employee) => $employee->whereIn('facility_id', $facilityIds));
        $this->applyStatusFilter($query, $filters);

        return $this->paginateList(
            $query,
            $filters,
            ['license_number'],
            [
                'employee.profile' => ['full_name'],
                'employee.facility' => ['name'],
            ]
        );
    }

    public function find(int $id): LabStaff
    {
        return LabStaff::with(['employee.profile', 'employee.facility'])->findOrFail($id);
    }

    public function create(array $data): LabStaff
    {
        return LabStaff::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $staff = LabStaff::findOrFail($id);
        return $staff->update($data);
    }

    public function delete(int $id): bool
    {
        $staff = LabStaff::findOrFail($id);
        return $staff->delete();
    }

    private function applyStatusFilter($query, array $filters): void
    {
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->whereHas('employee', fn($employee) => $employee->where(
                'is_active',
                in_array(strtolower($filters['status']), ['active', '1', 'true'], true)
            ));
        }
    }
}
