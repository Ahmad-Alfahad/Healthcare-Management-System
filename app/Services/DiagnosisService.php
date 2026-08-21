<?php

namespace App\Services;

use App\Repositories\DiagnosisRepository;
use App\Repositories\VisitRepository;
use App\Models\Diagnosis;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DiagnosisService
{
    protected  DiagnosisRepository $diagnosisRepository;
    protected  VisitRepository $visitRepository;

    public function __construct(DiagnosisRepository $diagnosisRepository, VisitRepository $visitRepository)
    {
        $this->diagnosisRepository = $diagnosisRepository;
        $this->visitRepository = $visitRepository;
    }

    public function getAllDiagnoses(User $user): Collection
    {
        if ($user->isAdmin()) {
            return $this->diagnosisRepository->all();
        }

        if ($user->isManager()) {
            $facility = $user->facility();

            return $facility
                ? $this->diagnosisRepository->getByFacility($facility->id)
                : new Collection();
        }

        if ($user->isDoctor()) {
            $doctor = $user->doctor;

            return $doctor
                ? $this->diagnosisRepository->getByDoctor($doctor->id)
                : new Collection();
        }

        if ($user->isPatient()) {
            $patient = $user->patient;

            return $patient
                ? $this->diagnosisRepository->getByPatient($patient->id)
                : new Collection();
        }

        return new Collection();
    }

    public function getDiagnosisById(int $id): Diagnosis
    {
        return $this->diagnosisRepository->find($id);
    }

    public function createDiagnosis(array $data): Diagnosis
    {
        $visit = $this->visitRepository
            ->find($data['visit_id']);
        $this->validateVisitAllowsDiagnosisCreation(
            $visit
        );
        $this->validatePrimaryDiagnosis(
            $data['visit_id'],
            $data['diagnosis_type']
        );
        $this->validateDiagnosisCodeUniqueness(
            $data['visit_id'],
            $data['diagnosis_code']
        );

        return $this->diagnosisRepository->create($data);
    }

    public function updateDiagnosis(int $id, array $data): bool
    {
        $diagnosis = $this->diagnosisRepository
            ->find($id);

        $visit = $this->visitRepository
            ->find($diagnosis->visit_id);

        $this->validateVisitAllowsDiagnosisUpdate(
            $visit
        );


        if (isset($data['diagnosis_type'])) {

            $this->validatePrimaryDiagnosisUpdate(
                $diagnosis->visit_id,
                $data['diagnosis_type'],
                $id
            );
        }

        if (isset($data['diagnosis_code'])) {

            $this->validateDiagnosisCodeUniquenessUpdate(
                $diagnosis->visit_id,
                $data['diagnosis_code'],
                $id
            );
        }
        return $this->diagnosisRepository->update($id, $data);
    }

    public function deleteDiagnosis(int $id): bool
    {
        $diagnosis = $this->diagnosisRepository
            ->find($id);

        $visit = $this->visitRepository
            ->find($diagnosis->visit_id);

        $this->validateVisitAllowsDiagnosisDeletion(
            $visit
        );
        return $this->diagnosisRepository->delete($id);
    }

    private function validatePrimaryDiagnosis(int $visitId, string $diagnosisType): void
    {
        if ($diagnosisType !== 'primary') {
            return;
        }

        if (
            $this->diagnosisRepository
            ->existsPrimaryDiagnosis($visitId)
        ) {
            throw ValidationException::withMessages([
                'diagnosis_type' => [
                    'This visit already has a primary diagnosis.'
                ]
            ]);
        }
    }

    private function validateVisitAllowsDiagnosisCreation(Visit $visit): void
    {
        if ($visit->status !== 'in_progress') {

            throw ValidationException::withMessages([
                'visit_id' => [
                    'Diagnosis can only be added to an active visit.'
                ]
            ]);
        }
    }

    private function validateDiagnosisCodeUniqueness(int $visitId, string $diagnosisCode): void
    {
        if (
            $this->diagnosisRepository
            ->existsDiagnosisCode(
                $visitId,
                $diagnosisCode
            )
        ) {
            throw ValidationException::withMessages([
                'diagnosis_code' => [
                    'This diagnosis code already exists for this visit.'
                ]
            ]);
        }
    }

    private function validatePrimaryDiagnosisUpdate(int $visitId, string $diagnosisType, int $diagnosisId): void
    {
        if ($diagnosisType !== 'primary') {
            return;
        }

        if (
            $this->diagnosisRepository
            ->existsPrimaryDiagnosisExcept(
                $visitId,
                $diagnosisId
            )
        ) {
            throw ValidationException::withMessages([
                'diagnosis_type' => [
                    'This visit already has a primary diagnosis.'
                ]
            ]);
        }
    }

    private function validateDiagnosisCodeUniquenessUpdate(int $visitId, string $diagnosisCode, int $diagnosisId): void
    {
        if (
            $this->diagnosisRepository
            ->existsDiagnosisCodeExcept(
                $visitId,
                $diagnosisCode,
                $diagnosisId
            )
        ) {
            throw ValidationException::withMessages([
                'diagnosis_code' => [
                    'This diagnosis code already exists for this visit.'
                ]
            ]);
        }
    }

    private function validateVisitAllowsDiagnosisUpdate(Visit $visit): void
    {
        if ($visit->status === 'cancelled') {

            throw ValidationException::withMessages([
                'visit_id' => [
                    'Diagnosis cannot be modified for a cancelled visit.'
                ]
            ]);
        }
    }

    private function validateVisitAllowsDiagnosisDeletion(Visit $visit): void
    {
        if ($visit->status !== 'in_progress') {

            throw ValidationException::withMessages([
                'visit_id' => [
                    'Diagnosis can only be deleted during an active visit.'
                ]
            ]);
        }
    }
}
