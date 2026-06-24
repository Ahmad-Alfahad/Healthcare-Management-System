<?php

namespace App\Repositories;

use App\Models\FacilityDepartmentSpecialization;
use Illuminate\Database\Eloquent\Collection;

class FacilityDepartmentSpecializationRepository
{
    public function all(): Collection
    {
        return FacilityDepartmentSpecialization::with(['specialization', 'facilityDepartment'])->get();
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
        $doctor_Schedule = FacilityDepartmentSpecialization::findOrFail($id);
        return $doctor_Schedule->update($data);
    }

    public function delete(int $id): bool
    {
        $doctor_schedule = FacilityDepartmentSpecialization::findOrFail($id);
        return $doctor_schedule->delete();
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
