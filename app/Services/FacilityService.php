<?php

namespace App\Services;

use App\Repositories\FacilityRepository;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class FacilityService
{
    protected FacilityRepository $facilityRepository;

    public function __construct(FacilityRepository $facilityRepository)
    {
        $this->facilityRepository = $facilityRepository;
    }

    public function getAllFacilities(): Collection
    {
        return $this->facilityRepository->all();
    }

    public function getFacilityById(int $id): ?Facility
    {
        return $this->facilityRepository->find($id);
    }

    public function createFacility(array $data): Facility
    {
        $parent = null;

        if (
            !empty($data['parent_id'])
        ) {

            $parent =
                $this->facilityRepository
                ->find(
                    $data['parent_id']
                );

            $this->validateParentFacility(
                $parent
            );
        }
        return $this->facilityRepository->create($data);
    }

    public function updateFacility(int $id, array $data): bool
    {
        $facility =
            $this->facilityRepository
            ->find($id);

        $parentId =
            $data['parent_id']
            ?? $facility->parent_id;

        $this->validateSelfParent(
            $facility->id,
            $parentId
        );

        $this->validateCircularReference(
            $facility->id,
            $parentId
        );
        if ($parentId) {

            $parent =
                $this->facilityRepository
                ->find($parentId);

            $this->validateParentFacility(
                $parent
            );
        }

        return $this->facilityRepository->update($id, $data);
    }

    public function deleteFacility(int $id): bool
    {
        $facility =
            $this->facilityRepository->find($id);

        $this->validateHasNoChildren(
            $facility
        );

        $this->validateHasNoDepartments(
            $facility
        );

        return $this->facilityRepository->delete($id);
    }

    private function validateParentFacility(?Facility $parent): void
    {
        if (!$parent) {
            return;
        }

        if (
            $parent->facility_type !==
            'hospital'
        ) {

            throw ValidationException::withMessages([
                'parent_id' => [
                    'Parent facility must be a hospital.'
                ]
            ]);
        }

        if (
            !$parent->is_active
        ) {

            throw ValidationException::withMessages([
                'parent_id' => [
                    'Parent facility is inactive.'
                ]
            ]);
        }
    }

    private function validateSelfParent(int $facilityId, ?int $parentId): void
    {
        if (
            $parentId !== null &&
            $facilityId === $parentId
        ) {
            throw ValidationException::withMessages([
                'parent_id' => [
                    'Facility cannot be its own parent.'
                ]
            ]);
        }
    }

    private function validateCircularReference(int $facilityId, ?int $parentId): void
    {
        if ($parentId === null) {
            return;
        }

        $currentParent =
            Facility::find($parentId);

        while ($currentParent) {

            if (
                $currentParent->id === $facilityId
            ) {
                throw ValidationException::withMessages([
                    'parent_id' => [
                        'Circular reference detected.'
                    ]
                ]);
            }

            $currentParent =
                $currentParent->parent;
        }
    }

    private function validateHasNoChildren(Facility $facility): void
    {
        if (
            $facility->childrens()
            ->exists()
        ) {

            throw ValidationException::withMessages([
                'facility' => [
                    'Cannot delete facility because it has child facilities.'
                ]
            ]);
        }
    }

    private function validateHasNoDepartments(Facility $facility): void
    {
        if (
            $facility->facilityDepartments()
            ->exists()
        ) {

            throw ValidationException::withMessages([
                'facility' => [
                    'Cannot delete facility because departments are assigned to it.'
                ]
            ]);
        }
    }
}
