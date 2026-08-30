<?php

namespace App\Services;

use App\Repositories\FacilityRepository;
use App\Models\Department;
use App\Models\Facility;
use App\Models\FacilityDepartment;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Pharmacist;
use App\Models\LabStaff;
use Illuminate\Validation\ValidationException;

class FacilityService
{
    protected FacilityRepository $facilityRepository;

    public function __construct(FacilityRepository $facilityRepository)
    {
        $this->facilityRepository = $facilityRepository;
    }

    public function getAllFacilities(User $user, array $filters = [])
    {
        if ($user->isManager()) {
            $facility = $user->facility();

            if (!$facility) {
                $filters['id'] = -1;
            } else {
                $filters['id'] = $facility->id;
            }
        }

        return $this->facilityRepository->all($filters);
    }

    public function getFacilityById(int $id): ?Facility
    {
        return $this->facilityRepository->find($id);
    }

    public function getFacilityStaff(
        int $facilityId,
        ?string $search = null,
        int $page = 1,
        int $perPage = 10
    ): array {
        $this->facilityRepository->find($facilityId);

        return $this->facilityRepository->staff($facilityId, $search, $page, $perPage);
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
    public function getManager(Facility $facility): ?User
    {
        // البحث في المنشأة نفسها
        $manager = $this->findManagerInFacility($facility);

        if ($manager) {
            return $manager;
        }

        // إذا كانت Child → ابحث في Parent
        if ($facility->parent_id) {
            $manager = $this->findManagerInFacility($facility->parent);

            if ($manager) {
                return $manager;
            }
        }

        // إذا كانت Parent → ابحث في Children
        foreach ($facility->childrens as $child) {
            $manager = $this->findManagerInFacility($child);

            if ($manager) {
                return $manager;
            }
        }

        return null;
    }

    private function findManagerInFacility(Facility $facility): ?User
    {
        // Doctor
        $doctorManager = Doctor::whereHas('profile.user', function ($query) {
            $query->role('manager');
        })
            ->whereHas(
                'facilityDepartmentSpecialization.facilityDepartment',
                function ($query) use ($facility) {
                    $query->where('facility_id', $facility->id);
                }
            )
            ->with('profile.user')
            ->first();

        if ($doctorManager) {
            return $doctorManager->profile->user;
        }

        // Pharmacist
        $pharmacistManager = Pharmacist::where('facility_id', $facility->id)
            ->whereHas('profile.user', function ($query) {
                $query->role('manager');
            })
            ->with('profile.user')
            ->first();

        if ($pharmacistManager) {
            return $pharmacistManager->profile->user;
        }

        // Lab Staff
        $labStaffManager = LabStaff::where('facility_id', $facility->id)
            ->whereHas('profile.user', function ($query) {
                $query->role('manager');
            })
            ->with('profile.user')
            ->first();

        if ($labStaffManager) {
            return $labStaffManager->profile->user;
        }

        return null;
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


    // I will move direct data access methods to the repository

    public function getFacilityDepartmentById(int $facilityDepartmentId): FacilityDepartment
    {
        return FacilityDepartment::findOrFail($facilityDepartmentId);
    }
    public function addDepartment(int $facilityId, int $departmentId): FacilityDepartment
    {
        $this->facilityRepository->find($facilityId);
        Department::findOrFail($departmentId);

        $exists = FacilityDepartment::where(
            'facility_id',
            $facilityId
        )
            ->where(
                'department_id',
                $departmentId
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'department_id' => [
                    'Department already assigned.'
                ]
            ]);
        }

        return FacilityDepartment::create([
            'facility_id' => $facilityId,
            'department_id' => $departmentId,
        ]);
    }

    public function removeDepartment(int $facilityDepartmentId): bool
    {
        $facilityDepartment =
            FacilityDepartment::findOrFail(
                $facilityDepartmentId
            );

        if (
            $facilityDepartment
            ->facilityDepartmentSpecializations()
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'department' => [
                    'Department has assigned specializations.'
                ]
            ]);
        }

        return $facilityDepartment->delete();
    }

    public function getFacilityDepartments(int $facilityId)
    {
        $this->facilityRepository->find($facilityId);

        return FacilityDepartment::with('department')
            ->where('facility_id', $facilityId)
            ->get()
            ->pluck('department');
    }
}
