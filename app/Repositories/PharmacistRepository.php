<?php

namespace App\Repositories;

use App\Models\Pharmacist;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class PharmacistRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = Pharmacist::with(['employee.profile', 'employee.facility']);
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
        $query = Pharmacist::with(['employee.profile', 'employee.facility'])
            ->whereHas('employee', fn($employee) => $employee->whereIn('facility_id', $facilityIds));
        $this->applyStatusFilter($query, $filters);

        return $this->paginateList(
            $query,
            $filters,
            ['license_number'],
            ['employee.profile' => ['full_name'], 'employee.facility' => ['name']]
        );
    }

    public function find(int $id): Pharmacist
    {
        return Pharmacist::with(['employee.profile', 'employee.facility'])->findOrFail($id);
    }

    public function create(array $data): Pharmacist
    {
        return Pharmacist::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $pharmacist = Pharmacist::findOrFail($id);
        return $pharmacist->update($data);
    }

    public function delete(int $id): bool
    {
        $pharmacist = Pharmacist::findOrFail($id);
        return $pharmacist->delete();
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
