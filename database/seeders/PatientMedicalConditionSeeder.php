<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatientMedicalCondition;
use App\Models\Patient;
use App\Models\MedicalCondition;

class PatientMedicalConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patient = Patient::whereHas('profile', fn($query) => $query->where('national_number', '9876543210'))->firstOrFail();
        $diabetes = MedicalCondition::where('name', 'Diabetes')->firstOrFail();
        $hypertension = MedicalCondition::where('name', 'Hypertension')->firstOrFail();
        $asthma = MedicalCondition::where('name', 'Asthma')->firstOrFail();

        $patientMedicalConditions = [
            [
                'patient_id' => $patient->id,
                'medical_condition_id' => $diabetes->id,
                'notes' => 'Patient has type 2 diabetes and requires regular monitoring.',
                'diagnosed_at' => '2020-01-15',
            ],
            [
                'patient_id' => $patient->id,
                'medical_condition_id' => $hypertension->id,
                'notes' => 'Patient has a history of hypertension.',
                'diagnosed_at' => '2018-05-20',
            ],
            [
                'patient_id' => $patient->id,
                'medical_condition_id' => $asthma->id,
                'notes' => 'Patient has asthma and uses an inhaler.',
                'diagnosed_at' => '2019-03-10',
            ],
        ];
        foreach ($patientMedicalConditions as $condition) {
            PatientMedicalCondition::updateOrCreate(
                ['patient_id' => $condition['patient_id'], 'medical_condition_id' => $condition['medical_condition_id']],
                $condition
            );
        }
    }
}
