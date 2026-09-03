<?php

namespace App\Repositories;

use App\Models\Facility;
use App\Models\Doctor;
use App\Models\LabStaff;
use App\Models\Pharmacist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class FacilityRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Facility::with(['parent', 'childrens']), $filters, ['name', 'facility_type'], [], ['id' => 'id']);
    }

    public function find(int $id): ?Facility
    {
        return Facility::with('childrens')->findOrFail($id);
    }

    public function staff(int $facilityId, ?string $search = null, int $page = 1, int $perPage = 10): array
    {
        $profileFields = 'id,full_name';

        return [
            'doctors' => $this->doctorStaffQuery($facilityId, $search)
                ->with([
                    "employee.profile:{$profileFields}",
                    'facilityDepartmentSpecialization.specialization:id,name',
                    'facilityDepartmentSpecialization.facilityDepartment.department:id,name',
                ])
                ->paginate($perPage, ['*'], 'page', $page),
            'pharmacists' => $this->simpleStaffQuery(Pharmacist::query(), $facilityId, $search)
                ->with(                "employee.profile:{$profileFields}")
                ->paginate($perPage, ['*'], 'page', $page),
            'lab_staff' => $this->simpleStaffQuery(LabStaff::query(), $facilityId, $search)
                ->with(                "employee.profile:{$profileFields}")
                ->paginate($perPage, ['*'], 'page', $page),
        ];
    }

    private function doctorStaffQuery(int $facilityId, ?string $search): Builder
    {
        $query = Doctor::query()
            ->whereHas('employee', fn(Builder $employee) => $employee->where('is_active', true))
            ->whereHas(
                'facilityDepartmentSpecialization.facilityDepartment',
                fn(Builder $builder) => $builder->where('facility_id', $facilityId)
            );

        if ($search) {
            $query->where(function (Builder $builder) use ($search) {
                $builder->whereHas('employee.profile', fn(Builder $profile) => $profile->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('facilityDepartmentSpecialization.specialization', fn(Builder $specialization) => $specialization->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('facilityDepartmentSpecialization.facilityDepartment.department', fn(Builder $department) => $department->where('name', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    private function simpleStaffQuery(Builder $query, int $facilityId, ?string $search): Builder
    {
        $query->whereHas('employee', function (Builder $employee) use ($facilityId) {
            $employee->where('facility_id', $facilityId)
                ->where('is_active', true);
        });

        if ($search) {
            $query->whereHas(
                'employee.profile',
                fn(Builder $profile) => $profile->where('full_name', 'like', "%{$search}%")
            );
        }

        return $query;
    }

    public function create(array $data): Facility
    {
        return Facility::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $facility = Facility::findOrFail($id);
        return $facility->update($data);
    }

    public function delete(int $id): bool
    {
        $facility = Facility::findOrFail($id);
        return $facility->delete();
    }
}
