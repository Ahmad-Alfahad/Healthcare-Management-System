<?php

namespace Database\Seeders;

use App\Models\Dispensing;
use Illuminate\Database\Seeder;

class DispensingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dispensings = [
            [
                'prescription_item_id' => 1,
                'pharmacist_id' => 1,
                'quantity_dispensed' => 20,
                'dispensed_at' => now()->subDays(2),
            ],
            [
                'prescription_item_id' => 2,
                'pharmacist_id' => 1,
                'quantity_dispensed' => 21,
                'dispensed_at' => now()->subDays(1),
            ],
            [
                'prescription_item_id' => 3,
                'pharmacist_id' => 1,
                'quantity_dispensed' => 10,
                'dispensed_at' => now(),
            ],
        ];

        foreach ($dispensings as $dispensing) {
            Dispensing::create($dispensing);
        }
    }
}
