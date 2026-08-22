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
            ['name' => 'John Doe', 'password' => bcrypt('password123'), 'is_active' => true]
        );
        $user->syncRoles(['patient']);

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

        $secondUser = User::firstOrCreate(
            ['email' => 'patient.sara@example.com'],
            ['name' => 'Sara Haddad', 'password' => bcrypt('password123'), 'is_active' => true]
        );
        $secondUser->syncRoles(['patient']);

        $secondProfile = Profile::firstOrCreate(
            ['user_id' => $secondUser->id],
            [
                'full_name' => 'Sara Haddad',
                'national_number' => '9876543211',
                'phone' => '+963955555555',
                'gender' => 'female',
                'address' => 'Damascus, Mazzeh',
                'date_of_birth' => '1987-11-03',
            ]
        );

        Patient::firstOrCreate(
            ['profile_id' => $secondProfile->id],
            [
                'blood_type' => 'A+',
                'emergency_contact_name' => 'Omar Haddad',
                'emergency_contact_phone' => '+963966666666',
                'emergency_contact_relation' => 'brother',
            ]
        );
    }
}
