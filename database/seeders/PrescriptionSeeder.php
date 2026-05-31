<?php

namespace Database\Seeders;

use App\Models\Prescription;
use Illuminate\Database\Seeder;

class PrescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $prescriptions = [

            [
                'visit_id' => 1,
                'status' => 'pending',
                'notes' => 'Take medication after meals.',
            ],

            [
                'visit_id' => 2,
                'status' => 'dispensed',
                'notes' => 'Medication delivered successfully.',
            ],

            [
                'visit_id' => 3,
                'status' => 'cancelled',
                'notes' => 'Prescription cancelled by doctor.',
            ],



        ];

        foreach ($prescriptions as $prescription) {

            Prescription::create(
                $prescription
            );
        }
    }
}