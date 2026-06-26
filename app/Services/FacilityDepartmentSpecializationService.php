<?php

namespace App\Services;

use App\Repositories\FacilityDepartmentSpecializationRepository;
use App\Repositories\SpecializationRepository;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\FacilityDepartment;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class FacilityDepartmentSpecializationService
{
    protected FacilityDepartmentSpecializationRepository $repository;
    protected SpecializationRepository $specializationRepository;

    public function __construct(FacilityDepartmentSpecializationRepository $repository, SpecializationRepository $specializationRepository)
    {
        $this->repository = $repository;
        $this->specializationRepository = $specializationRepository;
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getById(int $id): FacilityDepartmentSpecialization
    {
        return $this->repository->find($id);
    }

    public function create(array $data): FacilityDepartmentSpecialization
    {
        $facilityDepartment =
            FacilityDepartment::with('department')
            ->findOrFail(
                $data['facility_department_id']
            );
        $specialization =
            $this->specializationRepository
            ->find(
                $data['specialization_id']
            );

        $this->validateDuplicateAssignment(
            $data['facility_department_id'],
            $data['specialization_id']
        );

        $this->validateDepartmentIsActive(
            $facilityDepartment
        );

        $this->validateSpecializationIsActive(
            $specialization
        );

        return $this->repository->create(
            $data
        );
    }

    public function update(int $id, array $data): bool
    {
        $facilityDepartmentSpecialization =
            $this->repository->find($id);

        $facilityDepartmentId =
            $data['facility_department_id']
            ?? $facilityDepartmentSpecialization->facility_department_id;


        $specializationId =
            $data['specialization_id']
            ?? $facilityDepartmentSpecialization->specialization_id;

        $facilityDepartment =
            FacilityDepartment::with('department')
            ->findOrFail(
                $facilityDepartmentId
            );

        $specialization =
            $this->specializationRepository
            ->find(
                $specializationId
            );

        $this->validateDuplicateAssignmentForUpdate(
            $facilityDepartmentId,
            $specializationId,
            $id
        );

        $this->validateDepartmentIsActive(
            $facilityDepartment
        );

        $this->validateSpecializationIsActive(
            $specialization
        );

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $facilityDepartmentSpecialization =
            $this->repository->find($id);

        $this->validateDeletion(
            $facilityDepartmentSpecialization
        );

        return $this->repository->delete($id);
    }

    private function validateDuplicateAssignment(int $facilityDepartmentId, int $specializationId): void
    {
        if (
            $this->repository->exists(
                $facilityDepartmentId,
                $specializationId
            )
        ) {

            throw ValidationException::withMessages([
                'specialization_id' => [
                    'Specialization already assigned.'
                ]
            ]);
        }
    }

    private function validateDepartmentIsActive(FacilityDepartment $facilityDepartment): void
    {
        if (
            !$facilityDepartment
                ->department
                ->is_active
        ) {

            throw ValidationException::withMessages([
                'facility_department_id' => [
                    'Department is inactive.'
                ]
            ]);
        }
    }

    private function validateSpecializationIsActive(Specialization $specialization): void
    {
        if (
            !$specialization->is_active
        ) {

            throw ValidationException::withMessages([
                'specialization_id' => [
                    'Specialization is inactive.'
                ]
            ]);
        }
    }

    private function validateDeletion(FacilityDepartmentSpecialization $facilityDepartmentSpecialization): void
    {
        if (
            $facilityDepartmentSpecialization
            ->doctors()
            ->exists()
        ) {

            throw ValidationException::withMessages([
                'facility_department_specialization_id' => [
                    'Cannot delete specialization assignment because doctors are assigned to it.'
                ]
            ]);
        }
    }

    private function validateDuplicateAssignmentForUpdate(int $facilityDepartmentId, int $specializationId, int $currentId): void
    {
        $exists =
            FacilityDepartmentSpecialization::where(
                'facility_department_id',
                $facilityDepartmentId
            )
            ->where(
                'specialization_id',
                $specializationId
            )
            ->where(
                'id',
                '!=',
                $currentId
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'specialization_id' => [
                    'Specialization already assigned.'
                ]
            ]);
        }
    }
}
