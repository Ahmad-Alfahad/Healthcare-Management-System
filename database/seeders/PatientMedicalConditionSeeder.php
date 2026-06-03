<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatientMedicalCondition;
class PatientMedicalConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patientMedicalConditions = [
            [
                'patient_id' => 1,
                'medical_condition_id' => 1,
                'notes' => 'Patient has a history of hypertension.',
                'diagnosed_at' => '2020-01-15',
            ],
            [
                'patient_id' => 1,
                'medical_condition_id' => 2,
                'notes' => 'Patient is diabetic and requires regular monitoring.',
                'diagnosed_at' => '2018-05-20',
            ],
            [
                'patient_id' => 1,
                'medical_condition_id' => 3,
                'notes' => 'Patient has asthma and uses an inhaler.',
                'diagnosed_at' => '2019-03-10',
            ],
        ];
        foreach ($patientMedicalConditions as $condition) {
            PatientMedicalCondition::create($condition);
        }
    }
}
