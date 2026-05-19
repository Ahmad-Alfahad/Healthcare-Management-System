<?php

namespace App\Services;

use App\Repositories\SpecializationRepository;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Collection;

class SpecializationService
{
    protected $specializationRepository;

    public function __construct(SpecializationRepository $specializationRepository)
    {
        $this->specializationRepository = $specializationRepository;
    }

    public function getAllSpecializations(): Collection
    {
        return $this->specializationRepository->all();
    }

    public function getSpecializationById(int $id): Specialization
    {
        return $this->specializationRepository->find($id);
    }

    public function createSpecialization(array $data): Specialization
    {
        return $this->specializationRepository->create($data);
    }

    public function updateSpecialization(int $id, array $data): bool
    {
        return $this->specializationRepository->update($id, $data);
    }

    public function deleteSpecialization(int $id): bool
    {
        return $this->specializationRepository->delete($id);
    }
}
