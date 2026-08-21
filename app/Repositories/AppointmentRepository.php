<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository

{
    public function get(): Collection
    {
        return Appointment::with([
            'patient',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ])->get();
    }

    public function getByFacility(int $facilityId): Collection
    {
        return Appointment::with([
            'patient',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ])
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
        return Appointment::with([
            'patient',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ])
            ->where('doctor_id', $doctorId)
            ->get();
    }

    public function getByPatient(int $patientId): Collection
    {
        return Appointment::with([
            'patient',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ])
            ->where('patient_id', $patientId)
            ->get();
    }

    public function find(int $id): Appointment
    {
        return Appointment::with([
            'patient',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ])->findOrFail($id);
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $appointment = Appointment::findOrFail($id);

        return $appointment->update($data);
    }

    public function delete(int $id): bool
    {
        $appointment = Appointment::findOrFail($id);

        return $appointment->delete();
    }

    public function getAppointmentsByDate(int $doctorId, string $date): Collection
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereDate('scheduled_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
    }
}
