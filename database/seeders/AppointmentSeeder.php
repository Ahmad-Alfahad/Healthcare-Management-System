<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = [

            [
                'patient_id'       => 1,
                'doctor_id'        => 1,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '09:00:00',
                'status'           => 'pending',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => 1,
                'doctor_id'        => 1,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '10:00:00',
                'status'           => 'confirmed',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => 1,
                'doctor_id'        => 1,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '11:00:00',
                'status'           => 'completed',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => 1,
                'doctor_id'        => 1,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '12:00:00',
                'status'           => 'cancelled',
                'reason'           => 'General Checkup',
            ],

            [
                'patient_id'       => 1,
                'doctor_id'        => 1,
                'scheduled_date' => now()->addDay()->format('Y-m-d'),
                'start_time' => '13:00:00',
                'status'           => 'pending',
                'reason'           => 'General Checkup',
            ],
        ];

        foreach ($appointments as $appointment) {

            Appointment::create($appointment);

        }
    }
}