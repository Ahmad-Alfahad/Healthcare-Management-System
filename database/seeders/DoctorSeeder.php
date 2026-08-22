<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Facility;
use App\Models\Department;
use App\Models\Specialization;
use App\Models\Profile;
use App\Models\FacilityDepartment;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospital = Facility::where('facility_type', 'hospital')->first();
        $clinic   = Facility::where('facility_type', 'clinic')->first();

        $erDept   = Department::where('name', 'like', '%Emergency%')->first();
        $opdDept  = Department::where('name', 'like', '%Outpatient%')->first();

        $cardioSpec = Specialization::where('name', 'like', '%Cardiology%')->first();
        $pediaSpec  = Specialization::where('name', 'like', '%Pediatrics%')->first();

        if (!$hospital || !$erDept || !$cardioSpec) {
            return;
        }


        $link1 = FacilityDepartment::firstOrCreate([
            'facility_id'   => $hospital->id,
            'department_id' => $erDept->id,
        ]);


        $link2 = FacilityDepartment::firstOrCreate([
            'facility_id'   => $clinic->id,
            'department_id' => $opdDept->id ?? $erDept->id,
        ]);



        $workConfig1 = FacilityDepartmentSpecialization::firstOrCreate([
            'facility_department_id' => $link1->id,
            'specialization_id'      => $cardioSpec->id,
        ]);

        $workConfig2 = FacilityDepartmentSpecialization::firstOrCreate([
            'facility_department_id' => $link2->id,
            'specialization_id'      => $pediaSpec->id ?? $cardioSpec->id,
        ]);

        $userDoctor1 = User::firstOrCreate(
            ['email' => 'ahmad.doctor@healthcare.com'],
            [
                'name' => 'Dr. Ahmad Al-Masri',
                'password' => Hash::make('password123'),

            ]
        );


        $userDoctor2 = User::firstOrCreate(
            ['email' => 'nour.doctor@healthcare.com'],
            [
                'name' => 'Dr. Nour Al-Huda',
                'password' => Hash::make('password123'),

            ]
        );

        $userDoctor1->syncRoles(['doctor']);
        $userDoctor2->syncRoles(['doctor']);



        $profileDoctor1 = Profile::firstOrCreate(
            ['national_number' => '010100223344'],
            [
                'user_id'       => $userDoctor1->id,
                'full_name'          => 'Dr. Ahmad Al-Masri',
                'phone'  => '+963911111111',
                'gender'        => 'male',
                'address'       => 'Damascus',
                'date_of_birth' => '1980-05-12',
            ]
        );

        $profileDoctor2 = Profile::firstOrCreate(
            ['national_number' => '020200556677'],
            [
                'user_id'       => $userDoctor2->id,
                'full_name'          => 'Dr. Nour Al-Huda',
                'phone'  => '+963922222222',
                'gender'        => 'female',
                'address'       => 'Aleppo',
                'date_of_birth' => '1988-09-20',
            ]
        );



        Doctor::updateOrCreate(
            ['profile_id' => $profileDoctor1->id],
            [
                'facility_department_specialization_id' => $workConfig1->id,
                'qualification'       => 'Board-certified cardiologist',
                'years_of_experience' => 12,
                'biography'           => 'Experienced cardiologist providing preventive and acute cardiovascular care.',
                'achievements'        => 'Published research on hypertension management.',
                'languages'           => ['Arabic', 'English'],
                'is_active'           => true,
            ]
        );

        Doctor::updateOrCreate(
            ['profile_id' => $profileDoctor2->id],
            [
                'facility_department_specialization_id' => $workConfig2->id,
                'qualification'       => 'Master of Pediatrics',
                'years_of_experience' => 8,
                'biography'           => 'Pediatrician focused on preventive care and childhood development.',
                'achievements'        => 'Published research on neonatal health.',
                'languages'           => ['Arabic', 'English', 'French'],
                'is_active'           => true,
            ]
        );
    }
}
