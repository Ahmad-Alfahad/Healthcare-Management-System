<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalCondition;

class MedicalConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [

            [
                'name' => 'Diabetes',
                'type' => 'chronic',
                'notes' => 'Diabetes Mellitus',
            ],

            [
                'name' => 'Hypertension',
                'type' => 'chronic',
                'notes' => 'High blood pressure',
            ],

            [
                'name' => 'Asthma',
                'type' => 'chronic',
                'notes' => null,
            ],

            [
                'name' => 'Penicillin',
                'type' => 'allergy',
                'notes' => 'Antibiotic allergy',
            ],

            [
                'name' => 'Peanuts',
                'type' => 'allergy',
                'notes' => 'Food allergy',
            ],
        ];

        foreach ($conditions as $condition) {

            MedicalCondition::firstOrCreate(
                [
                    'name' => $condition['name'],
                    'type' => $condition['type'],
                ],
                [
                    'notes' => $condition['notes'],
                ]
            );
        }

    }
}
