<?php

namespace App\Services;

use App\Repositories\PharmacistRepository;
use App\Repositories\FacilityRepository;
use App\Models\Pharmacist;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class PharmacistService
{
    protected PharmacistRepository $pharmacistRepository;
    protected FacilityRepository $facilityRepository;

    public function __construct(PharmacistRepository $pharmacistRepository, FacilityRepository $facilityRepository)
    {
        $this->pharmacistRepository = $pharmacistRepository;
        $this->facilityRepository = $facilityRepository;
    }

    public function getAllPharmacists(User $user): Collection
    {
        if ($user->isManager()) {
            return $user->facility()
                ? $this->pharmacistRepository->getByFacility($user->facility()->id)
                : new Collection();
        }

        return $this->pharmacistRepository->all();
    }

    public function getPharmacistById(int $id): Pharmacist
    {
        return $this->pharmacistRepository->find($id);
    }

    public function createPharmacist(array $data): Pharmacist
    {
        $facility =
            $this->facilityRepository
            ->find(
                $data['facility_id']
            );
        $this->validateProfileNotAssigned(
            $data['profile_id']
        );

        $this->validateFacilityIsActive(
            $facility
        );
        $this->validateFacilityType(
            $facility
        );
        return $this->pharmacistRepository->create($data);
    }

    public function updatePharmacist(int $id, array $data): bool
    {
        $pharmacist =
            $this->pharmacistRepository
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
            ?? $pharmacist->facility_id;

        $facility =
            $this->facilityRepository
            ->find($facilityId);

        $this->validateFacilityIsActive(
            $facility
        );

        $this->validateFacilityType(
            $facility
        );
        return $this->pharmacistRepository->update($id, $data);
    }

    public function deletePharmacist(int $id): bool
    {
        return $this->pharmacistRepository->delete($id);
    }

    private function validateProfileNotAssigned(int $profileId, ?int $ignoreId = null): void
    {
        $query = Pharmacist::where(
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
                    'Profile already assigned to a pharmacist.'
                ]
            ]);
        }
    }


    private function validateFacilityIsActive(
        Facility $facility
    ): void {
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
            !== 'pharmacy'
        ) {

            throw ValidationException::withMessages([
                'facility_id' => [
                    'Pharmacist must belong to a pharmacy.'
                ]
            ]);
        }
    }
}
