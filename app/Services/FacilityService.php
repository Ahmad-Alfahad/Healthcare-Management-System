<?php

namespace App\Services;

use App\Repositories\FacilityRepository;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Collection;

class FacilityService
{
    protected $facilityRepository;

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
        return $this->facilityRepository->create($data);
    }

    public function updateFacility(int $id, array $data): bool
    {
        return $this->facilityRepository->update($id, $data);
    }

    public function deleteFacility(int $id): bool
    {
        return $this->facilityRepository->delete($id);
    }
}
