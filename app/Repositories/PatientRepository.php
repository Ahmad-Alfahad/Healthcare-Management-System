<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class PatientRepository
{
    public function get(): Collection
    {
        return Patient::with('profile')->get();
    }

    public function getByFacility(int $facilityId): Collection
    {
        return Patient::with('profile')
            ->whereHas(
                'appointments.doctor.facilityDepartmentSpecialization.facilityDepartment',
                fn($query) => $query->where('facility_id', $facilityId)
            )
            ->get();
    }

    public function find(int $id): Patient
    {
        return Patient::with('profile')->findOrFail($id);
    }

    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $patient = Patient::findOrFail($id);

        return $patient->update($data);
    }

    public function delete(int $id): bool
    {
        $patient = Patient::findOrFail($id);

        return $patient->delete();
    }
}
