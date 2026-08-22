<?php

namespace App\Services;

use App\Repositories\PatientRepository;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class PatientService
{
    protected PatientRepository $patientRepository;

    public function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function getAllPatients(User $user, array $filters = [])
    {
        if ($user->isManager()) {
            $facility = $user->facility();

            return $facility
                ? $this->patientRepository->getByFacility($facility->id, $filters)
                : $this->patientRepository->getByFacility(-1, $filters);
        }

        return $this->patientRepository->get($filters);
    }

    public function getPatient(int $id): Patient
    {
        return $this->patientRepository->find($id);
    }

    public function createPatient(array $data): Patient
    {
        $this->validateProfileNotAssigned(
            $data['profile_id']
        );
        return $this->patientRepository->create($data);
    }

    public function updatePatient(int $id, array $data): bool
    {

        if (
            isset($data['profile_id'])
        ) {

            $this->validateProfileNotAssigned(
                $data['profile_id'],
                $id
            );
        }
        return $this->patientRepository->update($id, $data);
    }

    public function deletePatient(int $id): bool
    {
        $patient = $this->patientRepository->find($id);
        $this->validateCanDelete($patient);
        return $this->patientRepository->delete($id);
    }

    private function validateProfileNotAssigned(int $profileId, ?int $ignoreId = null): void
    {
        $query = Patient::where(
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
                    'Profile already assigned to a patient.'
                ]
            ]);
        }
    }

    private function validateCanDelete(Patient $patient): void
    {
        if (
            $patient->appointments()->exists()
        ) {

            throw ValidationException::withMessages([
                'patient' => [
                    'Patient has appointments.'
                ]
            ]);
        }

        if (
            $patient->visits()->exists()
        ) {

            throw ValidationException::withMessages([
                'patient' => [
                    'Patient has visits.'
                ]
            ]);
        }
    }
}
