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
                $user->accessibleFacilityIds()
            );
        }

        if ($user->isDoctor() && $user->doctor) {
            return $this->doctorscheduleRepository->getByDoctor(
                $user->doctor->id
            );
        }

        if ($user->isPatient() && isset($filters['doctor_id'])) {
            return $this->doctorscheduleRepository->getByDoctor(
                $filters['doctor_id']
            );
        }

        return new Collection();
    }

    public function getDoctorScheduleById(int $id): DoctorSchedule
    {
        return $this->doctorscheduleRepository->find($id);
    }

    public function createDoctorSchedule(array $data, User $user): DoctorSchedule
    {
        $data['user_id'] = $user->id;

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
