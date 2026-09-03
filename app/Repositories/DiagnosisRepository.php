<?php

namespace App\Repositories;

use App\Models\Diagnosis;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class DiagnosisRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Diagnosis::with('visit.appointment'), $filters, ['diagnosis', 'status', 'diagnosed_at'], ['visit.patient.profile' => ['full_name'], 'visit.doctor.employee.profile' => ['full_name']]);
    }

    public function getByFacility(array $facilityIds, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Diagnosis::with('visit.appointment')
                ->whereHas(
                    'visit.doctor.facilityDepartmentSpecialization.facilityDepartment',
                    function ($query) use ($facilityIds) {
                        $query->whereIn('facility_id', $facilityIds);
                    }
                ),
            $filters,
            ['diagnosis', 'status', 'diagnosed_at'],
            ['visit.patient.profile' => ['full_name'], 'visit.doctor.employee.profile' => ['full_name']]
        );
    }

    public function getByDoctor(int $doctorId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Diagnosis::with('visit.appointment')
                ->whereHas('visit', function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }),
            $filters,
            ['diagnosis', 'status', 'diagnosed_at'],
            ['visit.patient.profile' => ['full_name'], 'visit.doctor.employee.profile' => ['full_name']]
        );
    }

    public function getByPatient(int $patientId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Diagnosis::with('visit.appointment')
                ->whereHas('visit', function ($query) use ($patientId) {
                    $query->where('patient_id', $patientId);
                }),
            $filters,
            ['diagnosis', 'status', 'diagnosed_at'],
            ['visit.patient.profile' => ['full_name'], 'visit.doctor.employee.profile' => ['full_name']]
        );
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
