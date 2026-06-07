<?php

namespace App\Services;

use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Models\DoctorSchedule;
use App\Repositories\DoctorScheduleRepository;


class AppointmentService
{

    protected $appointmentRepository;
    protected $doctorScheduleRepository;

    public function __construct(AppointmentRepository $appointmentRepository, DoctorScheduleRepository $doctorScheduleRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->doctorScheduleRepository = $doctorScheduleRepository;
    }

    public function getAllAppointments(): Collection
    {
        return $this->appointmentRepository->get();
    }

    public function getAppointment(int $id): Appointment
    {
        return $this->appointmentRepository->find($id);
    }

    public function createAppointment(array $data): Appointment
    {
        $this->validateAppointmentData($data['scheduled_date']);
        $this->validateDoctorSchedule($data['doctor_id'], $data['scheduled_date']);
        return $this->appointmentRepository->create($data);
    }

    public function updateAppointment(int $id, array $data): bool
    {
        return $this->appointmentRepository->update($id, $data);
    }

    public function deleteAppointment(int $id): bool
    {
        return $this->appointmentRepository->delete($id);
    }

    private function validateAppointmentData(string $scheduleDate): void
    {
        $currentDate = now()->startOfDay();
        $appointmentDate = Carbon::parse($scheduleDate)->startOfDay();

        if ($appointmentDate->lt($currentDate)) {
            throw new \InvalidArgumentException('Appointment date cannot be in the past.');
        }
    }

    private function validateDoctorSchedule( int $doctorId, string $date ): DoctorSchedule {
        $day = Carbon::parse($date)
            ->format('l');
        $schedule = $this->doctorScheduleRepository
            ->getDoctorScheduleByDay(
                $doctorId,
                $day
            );

        if (!$schedule) {

            throw ValidationException::withMessages([
                'doctor_id' => [
                    'Doctor is not available on this day.'
                ]
            ]);
        }

        return $schedule;
    }
}