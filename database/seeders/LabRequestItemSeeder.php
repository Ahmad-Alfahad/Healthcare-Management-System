<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabRequestItem;

class LabRequestItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labRequestItems = [
            [
                "visit_id" => 1,
                "lab_test_id" => 1,
                "requested_at" => now(),
                "notes" => "Fasting blood sugar test"
            ],
            [
                "visit_id" => 1,
                "lab_test_id" => 2,
                "requested_at" => now(),
                "notes" => "Complete blood count"
            ],
            [
                "visit_id" => 2,
                "lab_test_id" => 3,
                "requested_at" => now(),
                "notes" => "Lipid profile"
            ],
        ];

        foreach ($labRequestItems as $labRequestItem) {
            LabRequestItem::create($labRequestItem);
        }
    }
}
