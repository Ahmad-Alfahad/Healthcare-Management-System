<?php

namespace App\Services;

use App\Models\MedicalCondition;
use App\Repositories\MedicalConditionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class MedicalConditionService
{
    protected MedicalConditionRepository $medicalconditionRepository;

    public function __construct(MedicalConditionRepository $medicalconditionRepository)
    {
        $this->medicalconditionRepository = $medicalconditionRepository;
    }

    public function getAllMedicalCondition(array $filters = [])
    {
        return $this->medicalconditionRepository->all($filters);
    }

    public function getMedicalConditionById(int $id): MedicalCondition
    {
        return $this->medicalconditionRepository->find($id);
    }
    public function createMedicalCondition(array $data): MedicalCondition
    {
        $this->validateMedicalConditionUniqueness(
            $data['name'],
            $data['type']
        );
        return $this->medicalconditionRepository->create($data);
    }

    public function updateMedicalCondition(int $id, array $data): bool
    {
        $this->validateMedicalConditionUniquenessUpdate(
            $id,
            $data['name'],
            $data['type']
        );
        return $this->medicalconditionRepository->update($id, $data);
    }
    public function deleteMedicalCondition(int $id): bool
    {
        $this->validateMedicalConditionDeletion(
            $id
        );
        return $this->medicalconditionRepository->delete($id);
    }

    private function validateMedicalConditionUniqueness(string $name, string $type): void
    {
        if (
            $this->medicalconditionRepository
            ->existsByNameAndType(
                $name,
                $type
            )
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Medical condition already exists.'
                ]
            ]);
        }
    }

    private function validateMedicalConditionUniquenessUpdate(int $id, string $name, string $type): void
    {
        if (
            $this->medicalconditionRepository
            ->existsByNameAndTypeExcept(
                $name,
                $type,
                $id
            )
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Medical condition already exists.'
                ]
            ]);
        }
    }

    private function validateMedicalConditionDeletion(int $id): void
    {
        if (
            $this->medicalconditionRepository
            ->hasPatientMedicalConditions($id)
        ) {
            throw ValidationException::withMessages([
                'medical_condition_id' => [
                    'Cannot delete a medical condition that is assigned to patients.'
                ]
            ]);
        }
    }
}
