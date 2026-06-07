<?php
namespace App\Repositories;

use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Collection;

class DoctorScheduleRepository
{
    public function all(): Collection
    {
        return DoctorSchedule::with('doctor')->get();
    }

    public function find(int $id): DoctorSchedule
    {
        return DoctorSchedule::with('doctor')->findOrFail($id);
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

    
}