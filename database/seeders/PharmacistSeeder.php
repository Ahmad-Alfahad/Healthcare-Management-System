<?php

namespace Database\Seeders;

use App\Models\Pharmacist;
use App\Models\Facility;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PharmacistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinic = Facility::where('facility_type', 'pharmacy')->first();
        if (!$clinic) return;

        $user = User::firstOrCreate(
            ['email' => 'rawan.pharmacy@healthcare.com'],
            ['password' => Hash::make('password123'), 'name' => 'Rawan Al-Jamil']
        );
        $user->syncRoles(['pharmacist']);

        $profile = Profile::firstOrCreate(
            ['national_number' => '040400776655'],
            [
                'user_id'       => $user->id,
                'full_name'     => 'Rawan Al-Jamil',
                'phone'         => '+963944444444',
                'gender'        => 'female',
                'address'       => 'Latakia',
                'date_of_birth' => '1995-08-24'
            ]
        );

        Pharmacist::firstOrCreate(
            ['profile_id' => $profile->id],
            [
                'facility_id'         => $clinic->id,
                'degree'              => 'Doctor of Pharmacy (PharmD)',
                'years_of_experience' => 4,
                'license_number'      => 'LIC-PHARM-2026-4451',
                'is_active'           => true
            ]
        );
    }
}
