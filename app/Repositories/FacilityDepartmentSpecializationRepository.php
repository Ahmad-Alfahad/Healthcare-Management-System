<?php

namespace App\Repositories;

use App\Models\FacilityDepartmentSpecialization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FacilityDepartmentSpecializationRepository
{
    public function all(?User $user = null): Collection
    {
        $query = FacilityDepartmentSpecialization::with(['specialization', 'facilityDepartment']);
        if ($user?->isManager()) {
            $query->whereHas('facilityDepartment', fn($q) => $q->where('facility_id', $user->facility()?->id));
        }
        return $query->get();
    }

    public function find(int $id): FacilityDepartmentSpecialization
    {
        return FacilityDepartmentSpecialization::with(['specialization', 'facilityDepartment'])->findOrFail($id);
    }

    public function create(array $data): FacilityDepartmentSpecialization
    {
        return FacilityDepartmentSpecialization::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $facilityDepartmentSpecialization = FacilityDepartmentSpecialization::findOrFail($id);
        return $facilityDepartmentSpecialization->update($data);
    }

    public function delete(int $id): bool
    {
        $facilityDepartmentSpecialization = FacilityDepartmentSpecialization::findOrFail($id);
        return $facilityDepartmentSpecialization->delete();
    }

    public function exists(int $facilityDepartmentId, int $specializationId): bool
    {
        return FacilityDepartmentSpecialization::where(
            'facility_department_id',
            $facilityDepartmentId
        )
            ->where(
                'specialization_id',
                $specializationId
            )
            ->exists();
    }
}
