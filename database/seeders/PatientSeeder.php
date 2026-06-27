<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\Patient;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'patient.test@example.com'],
            ['name' => 'John Doe', 'password' => bcrypt('password')]
        );

        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'John Doe',
                'national_number' => '9876543210',
                'phone' => '+123456789',
                'gender' => 'male',
                'address' => 'Main Street, Aleppo',
                'date_of_birth' => '1990-05-15'
            ]
        );

        Patient::firstOrCreate(
            ['profile_id' => $profile->id],
            [
                'blood_type' => 'O+',
                'emergency_contact_name' => 'Jane Doe',
                'emergency_contact_phone' => '+987654321',
                'emergency_contact_relation' => 'father'
            ]
        );
    }
}