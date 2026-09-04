<?php

namespace App\Repositories;

use App\Models\Doctor;
use App\Support\ListQuery;
use Illuminate\Database\Eloquent\Builder;
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
        $query = $this->applyLocationFilters($this->baseQuery(), $filters);
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
        $query = $this->applyLocationFilters($query, $filters);
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

    private function applyLocationFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['facility_id']) && $filters['facility_id'] !== '') {
            $query->whereHas(
                'facilityDepartmentSpecialization.facilityDepartment',
                fn (Builder $facilityDepartment) => $facilityDepartment->where(
                    'facility_id',
                    $filters['facility_id']
                )
            );
        }

        if (isset($filters['department_id']) && $filters['department_id'] !== '') {
            $query->whereHas(
                'facilityDepartmentSpecialization.facilityDepartment',
                fn (Builder $facilityDepartment) => $facilityDepartment->where(
                    'department_id',
                    $filters['department_id']
                )
            );
        }

        if (isset($filters['specialization_id']) && $filters['specialization_id'] !== '') {
            $query->whereHas(
                'facilityDepartmentSpecialization',
                fn (Builder $assignment) => $assignment->where(
                    'specialization_id',
                    $filters['specialization_id']
                )
            );
        }

        return $query;
    }

    private function activeStatus(string $status): bool
    {
        return in_array(strtolower($status), ['active', '1', 'true'], true);
    }
}
