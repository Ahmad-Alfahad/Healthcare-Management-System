<?php

namespace Database\Seeders;

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
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            FacilitySeeder::class,
            ManagerSeeder::class,
            SpecializationSeeder::class,
            DepartmentSeeder::class,
            LabTestSeeder::class,
            PatientSeeder::class,
            DoctorSeeder::class,
            LabStaffSeeder::class,
            PharmacistSeeder::class,
            DoctorScheduleSeeder::class,
            MedicalConditionSeeder::class,
            PatientMedicalConditionSeeder::class,
            AppointmentSeeder::class,
            VisitSeeder::class,
            DiagnosisSeeder::class,
            PrescriptionSeeder::class,
            PrescriptionItemSeeder::class,
            LabRequestItemSeeder::class,
            LabResultSeeder::class,
            DispensingSeeder::class,
        ]);
    }
}
