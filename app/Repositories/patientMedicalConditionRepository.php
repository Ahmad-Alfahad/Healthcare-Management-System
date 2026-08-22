<?php

namespace App\Repositories;

use App\Models\PatientMedicalCondition;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientMedicalConditionRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(PatientMedicalCondition::with([
            'patient',
            'medicalCondition',
        ]), $filters, ['diagnosed_at'], ['patient.profile' => ['full_name'], 'medicalCondition' => ['name', 'type']]);
    }

    public function find(int $id): ?PatientMedicalCondition
    {
        return PatientMedicalCondition::with([
            'patient',
            'medicalCondition',
        ])->findOrFail($id);
    }

    public function create(array $data): PatientMedicalCondition
    {
        return PatientMedicalCondition::create($data);
    }

    public function update(PatientMedicalCondition $patientMedicalCondition, array $data): bool
    {
        return $patientMedicalCondition->update($data);
    }

    public function delete(PatientMedicalCondition $patientMedicalCondition): bool
    {
        return $patientMedicalCondition->delete();
    }

    public function existsForPatient(int $patientId, int $medicalConditionId): bool
    {
        return PatientMedicalCondition::where(
            'patient_id',
            $patientId
        )
            ->where(
                'medical_condition_id',
                $medicalConditionId
            )
            ->exists();
    }

    public function existsForPatientExcept(int $patientId, int $medicalConditionId, int $id): bool
    {
        return PatientMedicalCondition::where(
            'patient_id',
            $patientId
        )
            ->where(
                'medical_condition_id',
                $medicalConditionId
            )
            ->where(
                'id',
                '!=',
                $id
            )
            ->exists();
    }
}
