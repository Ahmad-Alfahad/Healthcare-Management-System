<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Auditable records require an authenticated user. Seeds are fixture data,
        // so events are suppressed only for this command; runtime auditing is unchanged.
        Model::withoutEvents(function (): void {
            $this->call([
                ReferenceDataSeeder::class,
                HealthcareScenarioSeeder::class,
            ]);
        });
    }
}
