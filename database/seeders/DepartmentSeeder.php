<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::firstOrCreate(
            ['name' => 'Emergency Department (ER)'],
            [
                'description' => 'Provides immediate treatment for acute illnesses and trauma 24/7.',
                'is_active'   => true
            ]
        );

        Department::firstOrCreate(
            ['name' => 'Outpatient Department (OPD)'],
            [
                'description' => 'Handles medical consultations, diagnostic tests, and minor surgical procedures that do not require overnight stays.',
                'is_active'   => true
            ]
        );

        Department::firstOrCreate(
            ['name' => 'Radiology & Medical Imaging'],
            [
                'description' => 'Equipped with X-ray, MRI, CT scan, and Ultrasound equipment for diagnostic scanning.',
                'is_active'   => true
            ]
        );

        Department::firstOrCreate(
            ['name' => 'Medical Laboratory & Pathology'],
            [
                'description' => 'Responsible for chemical, microscopic, and bacteriological tests of blood and tissues.',
                'is_active'   => true
            ]
        );

        Department::firstOrCreate(
            ['name' => 'Administration & Finance'],
            [
                'description' => 'Manages human resources, hospital billing, patient insurance affairs, and overall facility coordination.',
                'is_active'   => true
            ]
        );
    }
}
