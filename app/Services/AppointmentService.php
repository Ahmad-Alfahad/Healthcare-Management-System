<?php

namespace App\Services;

use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\AppointmentRepositoryInterface;

class AppointmentService
{

    protected $appointmentRepository;
       public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
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
        return $this->appointmentRepository->create($data);
    }

    public function updateAppointment( int $id, array $data ): bool {
        return $this->appointmentRepository->update($id, $data);
    }

    public function deleteAppointment(int $id): bool
    {
        return $this->appointmentRepository->delete($id);
    }
}