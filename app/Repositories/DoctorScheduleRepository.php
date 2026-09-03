<?php

namespace App\Repositories;

use App\Models\DoctorSchedule;
use App\Support\ListQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorScheduleRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(DoctorSchedule::with([
            'doctor.employee.profile',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility'
        ]), $filters, ['day_of_week', 'start_time', 'end_time'], ['doctor.employee.profile' => ['full_name'], 'doctor.facilityDepartmentSpecialization.facilityDepartment.facility' => ['name']]);
    }

    public function find(int $id): DoctorSchedule
    {
        return DoctorSchedule::with([
            'doctor.employee.profile',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility'
        ])->findOrFail($id);
    }

    public function create(array $data): DoctorSchedule
    {
        return DoctorSchedule::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $doctor_Schedule = DoctorSchedule::findOrFail($id);
        return $doctor_Schedule->update($data);
    }

    public function delete(int $id): bool
    {
        $doctor_schedule = DoctorSchedule::findOrFail($id);
        return $doctor_schedule->delete();
    }

    public function getDoctorScheduleByDay(int $doctorId, string $day): ?DoctorSchedule
    {
        return DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $day)
            ->first();
    }

    public function getByFacility(array $facilityIds): Collection
    {
        return DoctorSchedule::with([
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility'
        ])
            ->whereHas(
                'doctor.facilityDepartmentSpecialization.facilityDepartment',
                function ($query) use ($facilityIds) {
                    $query->whereIn('facility_id', $facilityIds);
                }
            )
            ->get();
    }

    public function getByDoctor(int $doctorId): Collection
    {
        return DoctorSchedule::with([
            'doctor.employee.profile',
            'doctor.facilityDepartmentSpecialization.facilityDepartment.facility'
        ])
            ->where('doctor_id', $doctorId)
            ->orderBy('day_of_week')
            ->get();
    }
}
