<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\DoctorScheduleRepository;
use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Collection;

class DoctorScheduleService
{
    protected DoctorScheduleRepository $doctorscheduleRepository;

    public function __construct(DoctorScheduleRepository $doctorscheduleRepository)
    {
        $this->doctorscheduleRepository = $doctorscheduleRepository;
    }

    public function getAllDoctorSchedules(User $user, array $filters = [])
    {
        if ($user->isAdmin()) {
            return $this->doctorscheduleRepository->all($filters);
        }

        if ($user->isManager()) {
            $facility = $user->facility();

            if (!$facility) {
                return new Collection();
            }

            return $this->doctorscheduleRepository->getByFacility(
                $facility->id
            );
        }

        return new Collection();
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
