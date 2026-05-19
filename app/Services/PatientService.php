<?php
namespace App\Services;

use App\Repositories\PatientRepository;
use App\Models\Patient;

class PatientService
{
    protected $patientRepository;

    public function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function jsonIndex()
    {
        return $this->patientRepository->getAll();
    }

    public function jsonShow(Patient $patient)
    {
        return $this->patientRepository->findById($patient);
    }

    public function jsonStore(array $data)
    {
        return $this->patientRepository->create($data);
    }

    public function jsonUpdate(Patient $patient, array $data)
    {
        return $this->patientRepository->update($patient, $data);
    }

    public function jsonDestroy(Patient $patient)
    {
        
        return $this->patientRepository->delete($patient);
    }
}