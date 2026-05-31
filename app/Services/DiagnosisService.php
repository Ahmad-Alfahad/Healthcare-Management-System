<?php
namespace App\Services;

use App\Repositories\DiagnosisRepository;
use App\Models\Diagnosis;

class DiagnosisService
{
    protected $diagnosisRepository;

    public function __construct(DiagnosisRepository $diagnosisRepository)
    {
        $this->diagnosisRepository = $diagnosisRepository;
    }

    public function getAllDiagnoses()
    {
        return $this->diagnosisRepository->all();
    }

    public function getDiagnosisById(int $id): Diagnosis
    {
        return $this->diagnosisRepository->find($id);
    }

    public function createDiagnosis(array $data): Diagnosis
    {
        return $this->diagnosisRepository->create($data);
    }

    public function updateDiagnosis(int $id, array $data): bool
    {
        return $this->diagnosisRepository->update($id, $data);
    }

    public function deleteDiagnosis(int $id): bool
    {
        return $this->diagnosisRepository->delete($id);
    }
}