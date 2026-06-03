<?php
namespace App\Services;

use App\Models\PatientMedicalCondition;
use App\Repositories\PatientMedicalConditionRepository;
use Illuminate\Database\Eloquent\Collection;

class PatientMedicalConditionService
{
    protected $repository;

    public function __construct(PatientMedicalConditionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getById(int $id): ?PatientMedicalCondition
    {
        return $this->repository->find($id);
    }
    
    public function create(array $data): PatientMedicalCondition
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ?PatientMedicalCondition
    {
        $patientMedicalCondition = $this->repository->find($id);
        if ($patientMedicalCondition) {
            $this->repository->update($patientMedicalCondition, $data);
            return $patientMedicalCondition;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $patientMedicalCondition = $this->repository->find($id);
        if ($patientMedicalCondition) {
            return $this->repository->delete($patientMedicalCondition);
        }
        return false;
    }
}