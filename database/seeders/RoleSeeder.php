<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['admin', 'manager', 'doctor', 'laboratory', 'pharmacist', 'patient'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $permissions = Permission::where('guard_name', 'web')->get()->keyBy('name');
        Role::findByName('admin', 'web')->syncPermissions($permissions->values());

        Role::findByName('manager', 'web')->syncPermissions(
            $permissions->filter(fn($permission, $name) => !str_starts_with($name, 'delete_'))->values()
        );

        Role::findByName('doctor', 'web')->syncPermissions($permissions->filter(
            fn($permission, $name) => str_contains($name, 'doctor')
                || str_contains($name, 'appointment')
                || str_contains($name, 'medical')
                || str_contains($name, 'prescription')
                || str_contains($name, 'lab_request')
                || $name === 'view_patient_history'
        )->values());

        Role::findByName('laboratory', 'web')->syncPermissions($permissions->filter(
            fn($permission, $name) => str_contains($name, 'lab_') || str_contains($name, 'medical_record')
        )->values());

        Role::findByName('pharmacist', 'web')->syncPermissions($permissions->filter(
            fn($permission, $name) => str_contains($name, 'prescription')
        )->values());

        Role::findByName('patient', 'web')->syncPermissions($permissions->filter(
            fn($permission, $name) => str_contains($name, 'appointment')
                || $name === 'view_patient_history'
                || $name === 'view_prescriptions'
                || $name === 'view_lab_requests'
        )->values());
    }
}
