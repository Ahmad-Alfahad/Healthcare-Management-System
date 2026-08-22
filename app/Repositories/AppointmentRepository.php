<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Appointment;
use App\Support\ListQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AppointmentRepository

{
    use ListQuery;

    public function get(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Appointment::with([
            'patient',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ]), $filters, ['status', 'reason', 'scheduled_date'], ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name'], 'doctor.facilityDepartmentSpecialization.specialization' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.facility' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.department' => ['name']]);
    }

    public function getByFacility(int $facilityId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Appointment::with([
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
                ),
            $filters,
            ['status', 'reason', 'scheduled_date'],
            ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name'], 'doctor.facilityDepartmentSpecialization.specialization' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.facility' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.department' => ['name']]
        );
    }

    public function getByDoctor(int $doctorId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Appointment::with([
                'patient',
                'doctor.facilityDepartmentSpecialization.specialization',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
            ])
                ->where('doctor_id', $doctorId),
            $filters,
            ['status', 'reason', 'scheduled_date'],
            ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name']]
        );
    }

    public function getByPatient(int $patientId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Appointment::with([
                'patient',
                'doctor.facilityDepartmentSpecialization.specialization',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
            ])
                ->where('patient_id', $patientId),
            $filters,
            ['status', 'reason', 'scheduled_date'],
            ['patient.profile' => ['full_name'], 'doctor.profile' => ['full_name']]
        );
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
