<?php

namespace App\Repositories;

use App\Models\FacilityDepartment;
use Illuminate\Database\Eloquent\Collection;

class FacilityDepartmentRepository
{
    public function all(): Collection
    {
        return FacilityDepartment::with(['facility', 'department'])->get();
    }

    public function find(int $id): FacilityDepartment
    {
        return FacilityDepartment::with(['facility', 'department'])->findOrFail($id);
    }

    public function create(array $data): FacilityDepartment
    {
        return FacilityDepartment::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $facilityDepartment = FacilityDepartment::findOrFail($id);
        return $facilityDepartment->update($data);
    }

    public function delete(int $id): bool
    {
        $facilityDepartment = FacilityDepartment::findOrFail($id);
        return $facilityDepartment->delete();
    }

    public function exists(int $facilityId, int $departmentId): bool
    {
        return FacilityDepartment::where('facility_id', $facilityId)
            ->where('department_id', $departmentId)
            ->exists();
    }

    public function existsExcept(int $facilityId, int $departmentId, int $id): bool
    {
        return FacilityDepartment::where('facility_id', $facilityId)
            ->where('department_id', $departmentId)
            ->where('id', '!=', $id)
            ->exists();
    }
}
