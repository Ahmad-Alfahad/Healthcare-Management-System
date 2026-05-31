<?php

namespace Database\Seeders;

use App\Models\Visit;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    public function run(): void
    {
        Visit::create([
            'appointment_id' => 1,
            'patient_id' => 1,
            'doctor_id' => 1,
            'notes' => 'General examination',
            'visited_at' => now(),
        ]);

        Visit::create([
            'appointment_id' => 2,
            'patient_id' => 1,
            'doctor_id' => 1,
            'notes' => 'Follow up visit',
            'visited_at' => now(),
        ]);

        Visit::create([
            'appointment_id' => 3,
            'patient_id' => 1,
            'doctor_id' => 1,
            'notes' => 'Routine checkup',
            'visited_at' => now(),
        ]);
    }
}