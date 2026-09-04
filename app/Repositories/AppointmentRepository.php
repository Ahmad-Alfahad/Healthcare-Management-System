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
            'patient.profile',
            'doctor.employee.profile',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ]), $filters, ['status', 'reason', 'scheduled_date'], ['patient.profile' => ['full_name'], 'doctor.employee.profile' => ['full_name'], 'doctor.facilityDepartmentSpecialization.specialization' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.facility' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.department' => ['name']]);
    }

    public function getByFacility(array $facilityIds, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Appointment::with([
                'patient.profile',
                'doctor.employee.profile',
                'doctor.facilityDepartmentSpecialization.specialization',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
            ])
                ->whereHas(
                    'doctor.facilityDepartmentSpecialization.facilityDepartment',
                    function ($query) use ($facilityIds) {
                        $query->whereIn('facility_id', $facilityIds);
                    }
                ),
            $filters,
            ['status', 'reason', 'scheduled_date'],
            ['patient.profile' => ['full_name'], 'doctor.employee.profile' => ['full_name'], 'doctor.facilityDepartmentSpecialization.specialization' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.facility' => ['name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.department' => ['name']]
        );
    }

    public function getByDoctor(int $doctorId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Appointment::with([
                'patient.profile',
                'doctor.employee.profile',
                'doctor.facilityDepartmentSpecialization.specialization',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
            ])
                ->where('doctor_id', $doctorId),
            $filters,
            ['status', 'reason', 'scheduled_date'],
            ['patient.profile' => ['full_name'], 'doctor.employee.profile' => ['full_name']]
        );
    }

    public function getByPatient(int $patientId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(
            Appointment::with([
                'patient.profile',
                'doctor.employee.profile',
                'doctor.facilityDepartmentSpecialization.specialization',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
                'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
            ])
                ->where('patient_id', $patientId),
            $filters,
            ['status', 'reason', 'scheduled_date'],
            ['patient.profile' => ['full_name'], 'doctor.employee.profile' => ['full_name']]
        );
    }

    public function find(int $id): Appointment
    {
        return Appointment::with([
            'patient.profile',
            'doctor.employee.profile',
            'doctor.facilityDepartmentSpecialization.specialization',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.department',
        ])->findOrFail($id);
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function existsPendingForPatientAndDoctor(
        int $patientId,
        int $doctorId
    ): bool {
        return Appointment::where('patient_id', $patientId)
            ->where('doctor_id', $doctorId)
            ->where('status', 'pending')
            ->exists();
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

    public function getConfirmed(?array $facilityIds = null, ?int $doctorId = null, ?int $patientId = null): Collection
    {
        $query = Appointment::with([
            'patient.profile',
            'doctor.employee.profile',
        ])
            ->where('status', 'confirmed')
            ->whereDate('scheduled_date', '>=', today())
            ->orderBy('scheduled_date')
            ->orderBy('start_time');

        if ($facilityIds !== null) {
            $query->whereHas(
                'doctor.facilityDepartmentSpecialization.facilityDepartment',
                fn ($facilityQuery) => $facilityQuery->whereIn('facility_id', $facilityIds)
            );
        }

        if ($doctorId !== null) {
            $query->where('doctor_id', $doctorId);
        }

        if ($patientId !== null) {
            $query->where('patient_id', $patientId);
        }

        return $query->get();
    }
}
