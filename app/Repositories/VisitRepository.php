<?php

namespace App\Repositories;

use App\Models\Visit;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class VisitRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Visit::with(['appointment', 'doctor', 'patient']), $filters, ['status', 'visited_at'], ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name']]);
    }

    public function getByFacility(int $facilityId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Visit::with(['appointment', 'doctor', 'patient'])
                ->whereHas(
                    'doctor.facilityDepartmentSpecialization.facilityDepartment',
                    function ($query) use ($facilityId) {
                        $query->where('facility_id', $facilityId);
                    }
                ),
            $filters,
            ['status', 'visited_at'],
            ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name']]
        );
    }

    public function getByDoctor(int $doctorId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Visit::with(['appointment', 'doctor', 'patient'])
                ->where('doctor_id', $doctorId),
            $filters,
            ['status', 'visited_at'],
            ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name']]
        );
    }

    public function getByPatient(int $patientId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Visit::with(['appointment', 'doctor', 'patient'])
                ->where('patient_id', $patientId),
            $filters,
            ['status', 'visited_at'],
            ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name']]
        );
    }

    public function find(int $id): Visit
    {
        return Visit::with(['appointment', 'doctor', 'patient'])->findOrFail($id);
    }

    public function create(array $data): Visit
    {
        return Visit::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $visit = Visit::findOrFail($id);
        return $visit->update($data);
    }

    public function delete(int $id): bool
    {
        $visit = Visit::findOrFail($id);
        return $visit->delete();
    }

    public function existsByAppointmentId(int $appointmentId): bool
    {
        return Visit::where(
            'appointment_id',
            $appointmentId
        )->exists();
    }

    public function findByAppointmentId(int $appointmentId): ?Visit
    {
        return Visit::where(
            'appointment_id',
            $appointmentId
        )->first();
    }
}
