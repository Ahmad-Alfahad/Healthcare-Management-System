<?php

namespace App\Services;

use App\Repositories\DoctorScheduleRepository;
use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Collection;

class DoctorScheduleService
{
    protected $doctorscheduleRepository;

    public function __construct(DoctorScheduleRepository $doctorscheduleRepository)
    {
        $this->doctorscheduleRepository = $doctorscheduleRepository;
    }

    public function getAllDoctorSchedules(): Collection
    {
        return $this->doctorscheduleRepository->all();
    }

    public function getDoctorScheduleById(int $id): DoctorSchedule
    {
        return $this->doctorscheduleRepository->find($id);
    }

    public function createDoctorSchedule(array $data): DoctorSchedule
    {
        return $this->doctorscheduleRepository->create($data);
    }

    public function updateDoctorSchedule(int $id, array $data): bool
    {
        return $this->doctorscheduleRepository->update($id, $data);
    }

    public function deleteDoctorSchedule(int $id): bool
    {
        return $this->doctorscheduleRepository->delete($id);
    }
}