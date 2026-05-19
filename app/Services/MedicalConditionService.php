<?php

namespace App\Services;

use App\Models\MedicalCondition;
use App\Repositories\MedicalConditionRepository;
use Illuminate\Database\Eloquent\Collection;

class MedicalConditionService
{
    protected $medicalconditionRepository;

    public function __construct(MedicalConditionRepository $medicalconditionRepository)
    {
        $this->medicalconditionRepository = $medicalconditionRepository;
    }

    public function getAllMedicalCondition(): Collection
    {
        return $this->medicalconditionRepository->all();
    }

    public function getMedicalConditionById(int $id): MedicalCondition
    {
        return $this->medicalconditionRepository->find($id);
    }
    public function createMedicalCondition(array $data): MedicalCondition
    {
        return $this->medicalconditionRepository->create($data);
    }

   public function updateMedicalCondition(int $id, array $data): bool
    {
        return $this->medicalconditionRepository->update($id, $data);
    }
    public function deleteMedicalCondition(int $id): bool
    {
        return $this->medicalconditionRepository->delete($id);
    }
}