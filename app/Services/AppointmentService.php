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
        $this->validateAppointmentDate($data['scheduled_date']);
        $schedule = $this->validateDoctorSchedule($data['doctor_id'], $data['scheduled_date']);
        $this->validateDoctorAvailability($schedule);
        $this->validateWorkingHours($schedule, $data['start_time']);
        $this->validateTimeSlot($schedule, $data['start_time']);
        $this->validateTimeConflict(
            $schedule,
            $data['doctor_id'],
            $data['scheduled_date'],
            $data['start_time']
        );
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

    private function validateAppointmentDate(string $scheduleDate): void
    {
        $currentDate = now()->startOfDay();
        $appointmentDate = Carbon::parse($scheduleDate)->startOfDay();

        if ($appointmentDate->lt($currentDate)) {
            throw new \InvalidArgumentException('Appointment date cannot be in the past.');
        }
    }

    private function validateDoctorSchedule(int $doctorId, string $date): DoctorSchedule
    {
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


    private function validateDoctorAvailability(DoctorSchedule $schedule): void
    {
        if ($schedule->is_off) {
            throw ValidationException::withMessages([
                'doctor_id' => [
                    'Doctor is unavailable on this day.'
                ]
            ]);
        }
    }

    private function validateWorkingHours(DoctorSchedule $schedule, string $appointmentTime): void
    {
        $appointmentTime = strtotime($appointmentTime);
        $startTime = strtotime($schedule->start_time);
        $endTime = strtotime($schedule->end_time);

        if ($appointmentTime < $startTime || $appointmentTime >= $endTime) {
            throw ValidationException::withMessages([
                'scheduled_date' => [
                    'Appointment time must be within doctor\'s working hours.'
                ]
            ]);
        }
    }

    private function validateTimeConflict(
        DoctorSchedule $schedule,
        int $doctorId,
        string $date,
        string $requestedTime
    ): void {

        $appointments = $this->appointmentRepository
            ->getAppointmentsByDate(
                $doctorId,
                $date
            );

        $requestStart = Carbon::createFromFormat(
            'H:i',
            $requestedTime
        );

        $requestEnd = $requestStart
            ->copy()
            ->addMinutes(
                $schedule->avg_consultation_time
            );

        if (
            $requestEnd->gt(
                Carbon::createFromFormat(
                    'H:i:s',
                    $schedule->end_time
                )
            )
        ) {
            throw ValidationException::withMessages([
                'start_time' => [
                    'Appointment exceeds doctor working hours.'
                ]
            ]);
        }

        foreach ($appointments as $appointment) {

            $existingStart = Carbon::createFromFormat(
                'H:i:s',
                $appointment->start_time
            );

            $existingEnd = $existingStart
                ->copy()
                ->addMinutes(
                    $schedule->avg_consultation_time
                );

            if (
                $requestStart->lt($existingEnd)
                &&
                $requestEnd->gt($existingStart)
            ) {
                throw ValidationException::withMessages([
                    'start_time' => [
                        'This time slot is already booked.'
                    ]
                ]);
            }
        }
    }

    private function validateTimeSlot(
        DoctorSchedule $schedule,
        string $requestedTime
    ): void {
        $workStart = Carbon::parse(
            $schedule->start_time
        );

        $appointmentTime = Carbon::parse(
            $requestedTime
        );

        $minutesDifference =
            $workStart->diffInMinutes(
                $appointmentTime
            );

        if (
            $minutesDifference %
            $schedule->avg_consultation_time !== 0
        ) {
            throw ValidationException::withMessages([
                'start_time' => [
                    'Invalid appointment slot.'
                ]
            ]);
        }
    }

    public function getAvailableSlots(int $doctorId, string $date): array
    {
        $day = Carbon::parse($date)
            ->format('l');

        $schedule = $this->doctorScheduleRepository
            ->getDoctorScheduleByDay(
                $doctorId,
                $day
            );

        if (!$schedule || $schedule->is_off) {
            return [];
        }

        $slots = [];

        $current = Carbon::parse(
            $schedule->start_time
        );

        $workEnd = Carbon::parse(
            $schedule->end_time
        );

        while ($current->lt($workEnd)) {

            $slotEnd = $current
                ->copy()
                ->addMinutes(
                    $schedule->avg_consultation_time
                );

            if ($slotEnd->gt($workEnd)) {
                break;
            }

            $slots[] = $current
                ->format('H:i:s');

            $current->addMinutes(
                $schedule->avg_consultation_time
            );
        }
        $appointments = $this->appointmentRepository
            ->getAppointmentsByDate(
                $doctorId,
                $date
            );

        $bookedSlots = $appointments
            ->pluck('start_time')
            ->toArray();
        return array_values(
            array_diff(
                $slots,
                $bookedSlots
            )
        );
    }

    public function changeStatus(int $appointmentId, string $newStatus): Appointment
    {
        $appointment = $this->appointmentRepository
                ->find($appointmentId);

        $this->validateStatusTransition(
            $appointment->status,
            $newStatus
        );

        $appointment->update([
            'status' => $newStatus
        ]);

        return $appointment->fresh();

    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $allowedTransitions = [

            'pending' => [
                'confirmed',
                'cancelled'
            ],

            'confirmed' => [
                'completed',
                'cancelled'
            ],

            'completed' => [],

            'cancelled' => [],
        ];

        if (
            !in_array(
                $newStatus,
                $allowedTransitions[$currentStatus]
            )
        ) {

            throw ValidationException::withMessages([
                'status' => [
                    'Invalid status transition.'
                ]
            ]);
        }
    }
}