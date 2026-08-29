<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::orderBy('id')->get();
        $doctors = Doctor::orderBy('id')->get();
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

        foreach ($patients as $index => $patient) {
            $doctor = $doctors[$index % $doctors->count()];
            $appointment = [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_date' => now()->subDays($index % 10)->format('Y-m-d'),
                'start_time' => sprintf('%02d:00:00', 8 + ($index % 9)),
                'status' => $statuses[$index % count($statuses)],
                'reason' => $index % 2 === 0 ? 'General checkup' : 'Follow-up consultation',
            ];
            Appointment::updateOrCreate(
                ['patient_id' => $appointment['patient_id'], 'doctor_id' => $appointment['doctor_id'], 'scheduled_date' => $appointment['scheduled_date'], 'start_time' => $appointment['start_time']],
                $appointment
            );
        }
    }
}
