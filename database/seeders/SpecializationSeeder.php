<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specialization::create([
            'name' => 'Cardiology'
        ]);
        Specialization::create([
            'name' => 'Neurology'
        ]);
        Specialization::create([
            'name' => 'Gastroenterology'
        ]);
        Specialization::create([
            'name' => 'Pediatrics'
        ]);
        Specialization::create([
            'name' => 'Dermatology'
        ]);
    }
}
