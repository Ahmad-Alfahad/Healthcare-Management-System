<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabTest;
class LabTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labTests = [
            [
                'name' => 'Complete Blood Count (CBC)',
                'range_high' => 10.5,
                'range_low' => 4.5,
                'unit' => 'x10^9/L'
            ],
            [
                'name' => 'Blood Glucose',
                'range_high' => 140,
                'range_low' => 70,
                'unit' => 'mg/dL'
            ],
            [
                'name' => 'Cholesterol',
                'range_high' => 200,
                'range_low' => 125,
                'unit' => 'mg/dL'
            ],
        ];

        foreach ($labTests as $labTest) {
            LabTest::create($labTest);
        }
    }
}
