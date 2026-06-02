<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            FacilitySeeder::class,
            RoleSeeder::class,
            SpecializationSeeder::class,
            DepartmentSeeder::class,
            DoctorSeeder::class,
            LabStaffSeeder::class,
            PharmacistSeeder::class,
            PatientSeeder::class,
            MedicalConditionSeeder::class,
            AppointmentSeeder::class,
            DoctorScheduleSeeder::class,
            VisitSeeder::class,
            DiagnosisSeeder::class,
            PrescriptionSeeder::class,
            PrescriptionItemSeeder::class,
            DispensingSeeder::class,
        ]);

    }
}
