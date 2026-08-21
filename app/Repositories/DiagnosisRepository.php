<?php

namespace App\Repositories;

use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Collection;

class DiagnosisRepository
{
    public function all(): Collection
    {
        return Diagnosis::with('visit.appointment')->get();
    }

    public function getByFacility(int $facilityId): Collection
    {
        return Diagnosis::with('visit.appointment')
            ->whereHas(
                'visit.doctor.facilityDepartmentSpecialization.facilityDepartment',
                function ($query) use ($facilityId) {
                    $query->where('facility_id', $facilityId);
                }
            )
            ->get();
    }

    public function getByDoctor(int $doctorId): Collection
    {
        return Diagnosis::with('visit.appointment')
            ->whereHas('visit', function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId);
            })
            ->get();
    }

    public function getByPatient(int $patientId): Collection
    {
        return Diagnosis::with('visit.appointment')
            ->whereHas('visit', function ($query) use ($patientId) {
                $query->where('patient_id', $patientId);
            })
            ->get();
    }

    public function find(int $id): Diagnosis
    {
        return Diagnosis::with('visit.appointment')->findOrFail($id);
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
