<?php

namespace App\Repositories;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;

class VisitRepository
{
    public function all(): Collection
    {
        return Visit::with(['appointment', 'doctor', 'patient'])->get();
    }

    public function getByFacility(int $facilityId): Collection
    {
        return Visit::with(['appointment', 'doctor', 'patient'])
            ->whereHas(
                'doctor.facilityDepartmentSpecialization.facilityDepartment',
                function ($query) use ($facilityId) {
                    $query->where('facility_id', $facilityId);
                }
            )
            ->get();
    }

    public function getByDoctor(int $doctorId): Collection
    {
        return Visit::with(['appointment', 'doctor', 'patient'])
            ->where('doctor_id', $doctorId)
            ->get();
    }

    public function getByPatient(int $patientId): Collection
    {
        return Visit::with(['appointment', 'doctor', 'patient'])
            ->where('patient_id', $patientId)
            ->get();
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
