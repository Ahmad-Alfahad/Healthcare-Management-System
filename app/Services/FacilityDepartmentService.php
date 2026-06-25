<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Facility;
use App\Models\FacilityDepartment;
use App\Repositories\FacilityDepartmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class FacilityDepartmentService
{
    protected FacilityDepartmentRepository $repository;

    public function __construct(FacilityDepartmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getById(int $id): FacilityDepartment
    {
        return $this->repository->find($id);
    }

    public function create(array $data): FacilityDepartment
    {
        $facility = Facility::findOrFail($data['facility_id']);
        $department = Department::findOrFail($data['department_id']);

        $this->validateDuplicateAssignment(
            $data['facility_id'],
            $data['department_id']
        );

        $this->validateFacilityIsActive($facility);
        $this->validateDepartmentIsActive($department);

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $facilityDepartment = $this->repository->find($id);

        $facilityId = $data['facility_id'] ?? $facilityDepartment->facility_id;
        $departmentId = $data['department_id'] ?? $facilityDepartment->department_id;

        $facility = Facility::findOrFail($facilityId);
        $department = Department::findOrFail($departmentId);

        $this->validateDuplicateAssignmentForUpdate(
            $facilityId,
            $departmentId,
            $id
        );

        $this->validateFacilityIsActive($facility);
        $this->validateDepartmentIsActive($department);

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $facilityDepartment = $this->repository->find($id);

        if ($facilityDepartment->facilityDepartmentSpecializations()->exists()) {
            throw ValidationException::withMessages([
                'facility_department_id' => [
                    'Cannot delete facility department because specializations are assigned to it.'
                ]
            ]);
        }

        return $this->repository->delete($id);
    }

    private function validateDuplicateAssignment(int $facilityId, int $departmentId): void
    {
        if ($this->repository->exists($facilityId, $departmentId)) {
            throw ValidationException::withMessages([
                'department_id' => [
                    'Department already assigned to this facility.'
                ]
            ]);
        }
    }

    private function validateDuplicateAssignmentForUpdate(int $facilityId, int $departmentId, int $id): void
    {
        if ($this->repository->existsExcept($facilityId, $departmentId, $id)) {
            throw ValidationException::withMessages([
                'department_id' => [
                    'Department already assigned to this facility.'
                ]
            ]);
        }
    }

    private function validateFacilityIsActive(Facility $facility): void
    {
        if (!$facility->is_active) {
            throw ValidationException::withMessages([
                'facility_id' => [
                    'Facility is inactive.'
                ]
            ]);
        }
    }

    private function validateDepartmentIsActive(Department $department): void
    {
        if (!$department->is_active) {
            throw ValidationException::withMessages([
                'department_id' => [
                    'Department is inactive.'
                ]
            ]);
        }
    }
}
