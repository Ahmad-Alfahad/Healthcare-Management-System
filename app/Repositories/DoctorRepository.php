<?php

namespace App\Repositories;

use App\Models\Doctor;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorRepository
{
    use ListQuery;

    private function baseQuery()
    {
        return Doctor::with([
            'employee.profile',
            'employee.facility',
            'facilityDepartmentSpecialization.specialization',
            'facilityDepartmentSpecialization.facilityDepartment.facility',
            'facilityDepartmentSpecialization.facilityDepartment.department',
        ]);
    }

    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->whereHas('employee', fn($employee) => $employee->where('is_active', $this->activeStatus($filters['status'])));
        }

        return $this->paginateList(
            $query,
            $filters,
            [
                'qualification',
                'biography',
            ],
            [
                'employee.profile' => ['full_name'],
                'employee.facility' => ['name'],
                'facilityDepartmentSpecialization.specialization' => ['name'],
                'facilityDepartmentSpecialization.facilityDepartment.department' => ['name'],
            ]
        );
    }

    public function find(int $id): Doctor
    {
        return $this->baseQuery()->findOrFail($id);
    }

    public function create(array $data): Doctor
    {
        return Doctor::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $doctor = Doctor::findOrFail($id);

        return $doctor->update($data);
    }

    public function delete(int $id): bool
    {
        $doctor = Doctor::findOrFail($id);

        return $doctor->delete();
    }

    public function getByFacility(
        array $facilityIds,
        array $filters = []
    ): LengthAwarePaginator {
        $query = $this->baseQuery()->whereHas(
            'employee',
            function ($query) use ($facilityIds) {
                $query->whereIn('facility_id', $facilityIds);
            }
        );
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->whereHas('employee', fn($employee) => $employee->where('is_active', $this->activeStatus($filters['status'])));
        }

        return $this->paginateList(
            $query,
            $filters,
            [
                'qualification',
                'biography',
            ],
            [
                'employee.profile' => ['full_name'],
                'employee.facility' => ['name'],
                'facilityDepartmentSpecialization.specialization' => ['name'],
                'facilityDepartmentSpecialization.facilityDepartment.department' => ['name'],
            ]
        );
    }

    private function activeStatus(string $status): bool
    {
        return in_array(strtolower($status), ['active', '1', 'true'], true);
    }
}
