<?php

namespace App\Services;

use App\Repositories\SpecializationRepository;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class SpecializationService
{
    protected SpecializationRepository $specializationRepository;

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
        $this->validateDepartmentUniqueness($data['name']);
        return $this->specializationRepository->create($data);
    }

    public function updateSpecialization(int $id, array $data): bool
    {
        if (isset($data['name'])) {
            $this->validateDepartmentUniquenessUpdate($id, $data['name']);
        }
        return $this->specializationRepository->update($id, $data);
    }

    public function deleteSpecialization(int $id): bool
    {
        return $this->specializationRepository->delete($id);
    }

    private function validateDepartmentUniqueness(string $name): void
    {
        if (
            $this->specializationRepository
            ->existsByName($name)
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Specialization already exists.'
                ]
            ]);
        }
    }

    private function validateDepartmentUniquenessUpdate(int $id, string $name): void
    {
        if (
            $this->specializationRepository
            ->existsByNameExcept(
                $name,
                $id
            )
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Specialization already exists.'
                ]
            ]);
        }
    }
}
