<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository
{
    use ListQuery;

    public function get(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Patient::with('profile'), $filters, [], ['profile' => ['full_name']]);
    }

    public function getByFacility(array $facilityIds, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Patient::with('profile')->whereHas('appointments.doctor.facilityDepartmentSpecialization.facilityDepartment', fn($query) => $query->whereIn('facility_id', $facilityIds)), $filters, [], ['profile' => ['full_name']]);
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
