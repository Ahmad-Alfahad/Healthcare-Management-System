<?php

namespace App\Repositories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository
    
{
    public function get(): Collection
    {
        return Appointment::with([
            'patient',
            'doctor',
        ])->get();
    }

    public function find(int $id): Appointment
    {
        return Appointment::with([
            'patient',
            'doctor',
        ])->findOrFail($id);
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
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
}