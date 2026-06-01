<?php

namespace Database\Seeders;

use App\Models\PrescriptionItem;
use Illuminate\Database\Seeder;

class PrescriptionItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [

            [
                'prescription_id' => 1,
                'medication_name' => 'Paracetamol',
                'dosage' => '500 mg',
                'quantity_prescribed' => 20,
                'frequency' => 'Twice daily',
                'duration' => 10,
            ],

            [
                'prescription_id' => 1,
                'medication_name' => 'Amoxicillin',
                'dosage' => '250 mg',
                'quantity_prescribed' => 21,
                'frequency' => 'Three times daily',
                'duration' => 7,
            ],

            [
                'prescription_id' => 2,
                'medication_name' => 'Omeprazole',
                'dosage' => '20 mg',
                'quantity_prescribed' => 30,
                'frequency' => 'Once daily',
                'duration' => 30,
            ],
        ];

        foreach ($items as $item) {
            PrescriptionItem::create($item);
        }
    }
}