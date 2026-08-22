<?php

namespace Database\Seeders;

use App\Models\LabStaff;
use App\Models\Facility;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LabStaffSeeder extends Seeder
{
    public function run(): void
    {
        $hospital = Facility::where('facility_type', 'laboratory')->first();
        if (!$hospital) return;

        $user = User::firstOrCreate(
            ['email' => 'sami.lab@healthcare.com'],
            [
                'password' => Hash::make('password123'),
                'name' => 'Sami Mansour'
            ]
        );
        $user->syncRoles(['laboratory']);

        $profile = Profile::firstOrCreate(
            ['national_number' => '030300998811'],
            [
                'user_id'       => $user->id,
                'full_name'     => 'Sami Mansour',
                'phone'         => '+963933333333',
                'gender'        => 'male',
                'address'       => 'Homs',
                'date_of_birth' => '1992-04-15'
            ]
        );

        LabStaff::firstOrCreate(
            ['profile_id' => $profile->id],
            [
                'facility_id'         => $hospital->id,
                'specialization'      => 'Clinical Biochemistry & Hematology',
                'degree'              => 'Bachelor of Science in Medical Laboratory Technology',
                'years_of_experience' => 6,
                'license_number'      => 'LIC-LAB-2026-9912',
                'is_active'           => true
            ]
        );
    }
}
