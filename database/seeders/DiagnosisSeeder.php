<?php

namespace Database\Seeders;

use App\Models\Diagnosis;
use Illuminate\Database\Seeder;

class DiagnosisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diagnoses = [

            [
                'visit_id' => 1,
                'diagnosis_code' => 'J11',
                'description' => 'Influenza (Flu)',
                'diagnosis_type' => 'primary',
                'notes' => 'Patient advised to rest and increase fluid intake.',
            ],

            [
                'visit_id' => 2,
                'diagnosis_code' => 'I10',
                'description' => 'Essential Hypertension',
                'diagnosis_type' => 'primary',
                'notes' => 'Monitor blood pressure regularly.',
            ],

            [
                'visit_id' => 3,
                'diagnosis_code' => 'E11',
                'description' => 'Type 2 Diabetes Mellitus',
                'diagnosis_type' => 'primary',
                'notes' => 'Recommend dietary modifications.',
            ],

            [
                'visit_id' => 3,
                'diagnosis_code' => 'J20',
                'description' => 'Acute Bronchitis',
                'diagnosis_type' => 'secondary',
                'notes' => 'Follow-up required after one week.',
            ],

        ];

        foreach ($diagnoses as $diagnosis) {
            Diagnosis::create($diagnosis);
        }
    }
}