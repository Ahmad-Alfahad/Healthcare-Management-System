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
        $patient = Patient::whereHas('profile', fn($query) => $query->where('national_number', '9876543210'))->firstOrFail();
        $doctorOne = Doctor::whereHas('profile', fn($query) => $query->where('national_number', '010100223344'))->firstOrFail();
        $doctorTwo = Doctor::whereHas('profile', fn($query) => $query->where('national_number', '020200556677'))->firstOrFail();

        $appointments = [

            [
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctorOne->id,
                'scheduled_date' => now()->subDay()->format('Y-m-d'),
                'start_time' => '09:00:00',
                'status'           => 'pending',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctorOne->id,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '10:00:00',
                'status'           => 'confirmed',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctorTwo->id,
                'scheduled_date' => now()->subDays(2)->format('Y-m-d'),
                'start_time' => '11:00:00',
                'status'           => 'completed',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctorTwo->id,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '12:00:00',
                'status'           => 'cancelled',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctorTwo->id,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '13:00:00',
                'status'           => 'pending',
                'reason'           => 'General Checkup',
            ],
        ];

        foreach ($appointments as $appointment) {

            Appointment::updateOrCreate(
                ['patient_id' => $appointment['patient_id'], 'doctor_id' => $appointment['doctor_id'], 'scheduled_date' => $appointment['scheduled_date'], 'start_time' => $appointment['start_time']],
                $appointment
            );
        }
    }
}
