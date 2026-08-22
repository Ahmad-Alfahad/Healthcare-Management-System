<?php

namespace App\Services;

use App\Repositories\LabStaffRepository;
use App\Repositories\FacilityRepository;
use App\Models\LabStaff;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class LabStaffService
{
    protected LabStaffRepository $labStaffRepository;
    protected FacilityRepository $facilityRepository;

    public function __construct(LabStaffRepository $labStaffRepository, FacilityRepository $facilityRepository)
    {
        $this->labStaffRepository = $labStaffRepository;
        $this->facilityRepository = $facilityRepository;
    }

    public function getAllStaff(User $user, array $filters = [])
    {
        if ($user->isManager()) {
            return $user->facility()
                ? $this->labStaffRepository->getByFacility($user->facility()->id, $filters)
                : $this->labStaffRepository->getByFacility(-1, $filters);
        }

        return $this->labStaffRepository->all($filters);
    }

    public function getStaffById(int $id): LabStaff
    {
        return $this->labStaffRepository->find($id);
    }

    public function createStaff(array $data): LabStaff
    {
        $facility =
            $this->facilityRepository
            ->find($data['facility_id']);
        $this->validateProfileNotAssigned(
            $data['profile_id']
        );
        $this->validateFacilityIsActive(
            $facility
        );

        $this->validateFacilityType(
            $facility
        );
        return $this->labStaffRepository->create($data);
    }

    public function updateStaff(int $id, array $data): bool
    {
        $labstaff =
            $this->labStaffRepository
            ->find($id);
        if (
            isset($data['profile_id'])
        ) {

            $this->validateProfileNotAssigned(
                $data['profile_id'],
                $id
            );
        }

        $facilityId =
            $data['facility_id']
            ?? $labstaff->facility_id;

        $facility =
            $this->facilityRepository
            ->find($facilityId);

        $this->validateFacilityIsActive(
            $facility
        );

        $this->validateFacilityType(
            $facility
        );
        return $this->labStaffRepository->update($id, $data);
    }

    public function deleteStaff(int $id): bool
    {
        return $this->labStaffRepository->delete($id);
    }

    private function validateProfileNotAssigned(int $profileId, ?int $ignoreId = null): void
    {
        $query = LabStaff::where(
            'profile_id',
            $profileId
        );

        if ($ignoreId) {
            $query->where(
                'id',
                '!=',
                $ignoreId
            );
        }

        if ($query->exists()) {

            throw ValidationException::withMessages([
                'profile_id' => [
                    'Profile already assigned to a Labstaff.'
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

    private function validateFacilityType(Facility $facility): void
    {
        if (
            $facility->facility_type
            !== 'laboratory'
        ) {

            throw ValidationException::withMessages([
                'facility_id' => [
                    'Lab staff must belong to a laboratory.'
                ]
            ]);
        }
    }
}
