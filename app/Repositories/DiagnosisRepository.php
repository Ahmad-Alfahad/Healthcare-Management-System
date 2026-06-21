<?php

namespace App\Repositories;

use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Collection;

class DiagnosisRepository
{
    public function all(): Collection
    {
        return Diagnosis::all();
    }

    public function find(int $id): Diagnosis
    {
        return Diagnosis::with('visit')->findOrFail($id);
    }

    public function create(array $data): Diagnosis
    {
        return Diagnosis::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $diagnosis = Diagnosis::findOrFail($id);
        return $diagnosis->update($data);
    }

    public function delete(int $id): bool
    {
        $diagnosis = Diagnosis::findOrFail($id);
        return $diagnosis->delete();
    }

    public function existsPrimaryDiagnosis(int $visitId): bool
    {
        return Diagnosis::where(
            'visit_id',
            $visitId
        )
            ->where(
                'diagnosis_type',
                'primary'
            )
            ->exists();
    }

    public function existsDiagnosisCode(int $visitId, string $diagnosisCode): bool
    {
        return Diagnosis::where(
            'visit_id',
            $visitId
        )
            ->where(
                'diagnosis_code',
                $diagnosisCode
            )
            ->exists();
    }

    public function existsPrimaryDiagnosisExcept(int $visitId, int $diagnosisId): bool
    {
        return Diagnosis::where(
            'visit_id',
            $visitId
        )
            ->where(
                'diagnosis_type',
                'primary'
            )
            ->where(
                'id',
                '!=',
                $diagnosisId
            )
            ->exists();
    }

    public function existsDiagnosisCodeExcept(int $visitId, string $diagnosisCode, int $diagnosisId): bool
    {
        return Diagnosis::where(
            'visit_id',
            $visitId
        )
            ->where(
                'diagnosis_code',
                $diagnosisCode
            )
            ->where(
                'id',
                '!=',
                $diagnosisId
            )
            ->exists();
    }
}
