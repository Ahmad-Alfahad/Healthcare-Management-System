<?php

namespace App\Services;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\PrescriptionRepository;

class PrescriptionService
{
     protected $prescriptionRepository;

    public function __construct(PrescriptionRepository $prescriptionRepository)
    {
        $this->prescriptionRepository = $prescriptionRepository;
    }

    public function getAllPrescriptions(): Collection
    {
        return $this->prescriptionRepository->all();
    }

    public function getPrescriptionById(int $id): Prescription
    {
        return $this->prescriptionRepository->find($id);
    }

    public function createPrescription(array $data): Prescription
    {
        return $this->prescriptionRepository->create($data);
    }

    public function updatePrescription(int $id, array $data): bool
    {
        return $this->prescriptionRepository->update($id, $data);
    }

    public function deletePrescription(int $id): bool
    {
        return $this->prescriptionRepository->delete($id);
    }

}