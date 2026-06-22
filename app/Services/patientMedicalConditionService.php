<?php

namespace App\Services;

use App\Models\PatientMedicalCondition;
use App\Repositories\PatientMedicalConditionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PatientMedicalConditionService
{
    protected PatientMedicalConditionRepository $repository;

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
        $this->validateConditionAssignment(
            $data['patient_id'],
            $data['medical_condition_id']
        );

        $this->validateDiagnosisDate(
            $data['diagnosed_at'] ?? null
        );
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ?PatientMedicalCondition
    {
        $patientMedicalCondition =
            $this->repository->find($id);

        $this->validateDiagnosisDate(
            $data['diagnosed_at'] ?? null
        );
        $this->repository->update(
            $patientMedicalCondition,
            $data
        );

        return $patientMedicalCondition->fresh();
    }

    public function delete(int $id): bool
    {
        $patientMedicalCondition =
            $this->repository->find($id);

        return $this->repository->delete(
            $patientMedicalCondition
        );
    }

    private function validateConditionAssignment(int $patientId, int $medicalConditionId): void
    {
        if (
            $this->repository
            ->existsForPatient(
                $patientId,
                $medicalConditionId
            )
        ) {
            throw ValidationException::withMessages([
                'medical_condition_id' => [
                    'This condition is already assigned to the patient.'
                ]
            ]);
        }
    }

    private function validateDiagnosisDate(?string $diagnosedAt): void
    {
        if (
            $diagnosedAt &&
            Carbon::parse($diagnosedAt)->isFuture()
        ) {
            throw ValidationException::withMessages([
                'diagnosed_at' => [
                    'Diagnosis date cannot be in the future.'
                ]
            ]);
        }
    }
}
