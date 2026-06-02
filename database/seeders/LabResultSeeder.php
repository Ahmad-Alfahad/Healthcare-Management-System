<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabResult;
class LabResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labResults = [
            [
                'lab_request_item_id' => 1,
                'lab_staff_id' => 1,
                'notes' => 'Normal blood sugar levels.',
                'status' => 'completed',
                'value' => '90',
                'unit' => 'mg/dL',
                'reference_range' => '70-100',
                'access_token' => null,
                'completed_at' => now(),
            ],
            [
                'lab_request_item_id' => 2,
                'lab_staff_id' => 1,
                'notes' => 'Elevated cholesterol levels.',
                'status' => 'completed',
                'value' => '240',
                'unit' => 'mg/dL',
                'reference_range' => '<200',
                'access_token' => null,
                'completed_at' => now(),
            ],
        ];

        foreach ($labResults as $labResult) {
            LabResult::create($labResult);
        }
    }
}
