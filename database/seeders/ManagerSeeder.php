<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Facility;
use App\Models\Pharmacist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        $facility = Facility::where('name', 'Al-Amal Community Pharmacy')->firstOrFail();

        $manager = User::updateOrCreate(
            ['email' => 'manager@healthcare.com'],
            [
                'name' => 'Healthcare Manager',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $profile = Profile::updateOrCreate(
            ['user_id' => $manager->id],
            [
                'full_name' => 'Healthcare Manager',
                'national_number' => '0000000002',
                'phone' => '+963911000002',
                'gender' => 'female',
                'address' => 'Damascus',
                'date_of_birth' => '1986-02-02',
            ]
        );

        Pharmacist::updateOrCreate(
            ['profile_id' => $profile->id],
            [
                'facility_id' => $facility->id,
                'degree' => 'Doctor of Pharmacy (PharmD)',
                'years_of_experience' => 10,
                'license_number' => 'LIC-MANAGER-PHARM-2026',
                'is_active' => true,
            ]
        );

        $manager->syncRoles(['manager', 'pharmacist']);
    }
}
