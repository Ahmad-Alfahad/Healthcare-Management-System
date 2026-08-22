<?php

namespace App\Services;

use App\Models\Visit;
use App\Repositories\VisitRepository;
use App\Repositories\AppointmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class VisitService
{
    protected VisitRepository $visitRepository;
    protected AppointmentRepository $appointmentRepository;
    public function __construct(VisitRepository $visitRepository, AppointmentRepository $appointmentRepository)
    {
        $this->visitRepository = $visitRepository;
        $this->appointmentRepository = $appointmentRepository;
    }

    public function getAllVisits(User $user, array $filters = [])
    {
        if ($user->isAdmin()) {
            return $this->visitRepository->all($filters);
        }

        if ($user->isManager()) {
            $facility = $user->facility();

            return $facility
                ? $this->visitRepository->getByFacility($facility->id, $filters)
                : new Collection();
        }

        if ($user->isDoctor()) {
            $doctor = $user->doctor;

            return $doctor
                ? $this->visitRepository->getByDoctor($doctor->id, $filters)
                : new Collection();
        }

        if ($user->isPatient()) {
            $patient = $user->patient;

            return $patient
                ? $this->visitRepository->getByPatient($patient->id, $filters)
                : new Collection();
        }

        return new Collection();
    }

    public function getVisitById(int $id): Visit
    {
        return $this->visitRepository->find($id);
    }

    public function createVisit(array $data): Visit
    {
        $appointment = $this->appointmentRepository
            ->find($data['appointment_id']);

        $this->validateVisitUniqueness(
            $appointment->id
        );

        $this->validateAppointmentStatus(
            $appointment
        );

        $this->validateAppointmentTimeReached(
            $appointment
        );

        $visitedAt = Carbon::parse(
            $appointment->scheduled_date . ' ' .
                $appointment->start_time
        );

        $data['doctor_id'] = $appointment->doctor_id;

        $data['patient_id'] = $appointment->patient_id;

        $data['status'] = 'in_progress';

        $data['visited_at'] = $visitedAt;

        return $this->visitRepository->create($data);
    }

    public function updateVisit(int $id, array $data): bool
    {
        $visit = $this->visitRepository->find($id);
        $this->validateVisitIsEditable($visit);
        return $this->visitRepository->update($id, $data);
    }

    public function deleteVisit(int $id): bool
    {
        $visit = $this->visitRepository->find($id);
        $this->validateVisitIsEditable($visit);
        return $this->visitRepository->delete($id);
    }


    private function validateVisitUniqueness(int $appointmentId): void
    {
        if (
            $this->visitRepository
            ->existsByAppointmentId($appointmentId)
        ) {
            throw ValidationException::withMessages([
                'appointment_id' => [
                    'This appointment already has a visit.'
                ]
            ]);
        }
    }

    private function validateAppointmentStatus(Appointment $appointment): void
    {
        if (
            $appointment->status !== 'confirmed'
        ) {
            throw ValidationException::withMessages([
                'appointment_id' => [
                    'Appointment must be confirmed.'
                ]
            ]);
        }
    }

    private function validateAppointmentTimeReached(Appointment $appointment): void
    {
        $appointmentDateTime = Carbon::parse(
            $appointment->scheduled_date . ' ' .
                $appointment->start_time
        );

        if (now()->lt($appointmentDateTime)) {
            throw ValidationException::withMessages([
                'appointment_id' => [
                    'Appointment time has not been reached yet.'
                ]
            ]);
        }
    }

    private function validateVisitStatusTransition(Visit $visit, string $newStatus): void
    {
        if (
            $visit->status !== 'in_progress'
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    'Visit status cannot be changed.'
                ]
            ]);
        }

        if (
            ! in_array(
                $newStatus,
                ['completed', 'cancelled']
            )
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    'Invalid visit status.'
                ]
            ]);
        }
    }

    public function changeStatus(int $visitId, string $newStatus): bool
    {
        $visit = $this->visitRepository
            ->find($visitId);

        $this->validateVisitStatusTransition(
            $visit,
            $newStatus
        );

        return $this->visitRepository
            ->update(
                $visitId,
                [
                    'status' => $newStatus
                ]
            );
    }

    private function validateVisitIsEditable(Visit $visit): void
    {
        if ($visit->status !== 'in_progress') {

            throw ValidationException::withMessages([
                'visit' => [
                    'Only in progress visits can be modified.'
                ]
            ]);
        }
    }
}
