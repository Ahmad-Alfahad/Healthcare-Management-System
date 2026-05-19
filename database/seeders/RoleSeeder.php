<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'phamacist', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'laboratory', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
    }
}
