<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Facility;
use App\Models\FacilityDepartment;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\LabTest;
use App\Models\MedicalCondition;
use App\Models\Specialization;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seedRolesAndPermissions();
        $facilities = $this->seedFacilities();
        $departments = $this->seedDepartments();
        $specializations = $this->seedSpecializations();
        $this->seedFacilityConfiguration($facilities, $departments, $specializations);
        $this->seedLabTests();
        $this->seedMedicalConditions();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function seedRolesAndPermissions(): void
    {
        $permissions = [
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_profiles', 'edit_profiles',
            'view_facilities', 'create_facilities', 'edit_facilities', 'delete_facilities',
            'view_doctors', 'create_doctors', 'edit_doctors', 'delete_doctors', 'manage_specializations',
            'view_appointments', 'create_appointments', 'edit_appointments', 'cancel_appointments', 'manage_doctor_schedules',
            'view_medical_records', 'create_medical_records', 'edit_medical_records', 'view_patient_history',
            'view_prescriptions', 'create_prescriptions', 'edit_prescriptions', 'dispense_prescriptions',
            'view_lab_requests', 'create_lab_requests', 'upload_lab_results', 'approve_lab_results',
            'manage_allergies', 'manage_chronic_diseases',
        ];

        foreach ($permissions as $name) {
            Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        foreach (['admin', 'manager', 'doctor', 'laboratory', 'pharmacist', 'patient'] as $name) {
            Role::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $all = Permission::query()->where('guard_name', 'web')->get();
        Role::findByName('admin')->syncPermissions($all);
        Role::findByName('manager')->syncPermissions($all->reject(fn (Permission $permission) => str_starts_with($permission->name, 'delete_')));
        Role::findByName('doctor')->syncPermissions($all->filter(fn (Permission $permission) => str_contains($permission->name, 'doctor')
            || str_contains($permission->name, 'appointment')
            || str_contains($permission->name, 'medical')
            || str_contains($permission->name, 'prescription')
            || str_contains($permission->name, 'lab_request')
            || $permission->name === 'view_patient_history'));
        Role::findByName('laboratory')->syncPermissions($all->filter(fn (Permission $permission) => str_contains($permission->name, 'lab_') || str_contains($permission->name, 'medical_record')));
        Role::findByName('pharmacist')->syncPermissions($all->filter(fn (Permission $permission) => str_contains($permission->name, 'prescription')));
        Role::findByName('patient')->syncPermissions($all->filter(fn (Permission $permission) => str_contains($permission->name, 'appointment')
            || in_array($permission->name, ['view_patient_history', 'view_prescriptions', 'view_lab_requests'], true)));
    }

    /** @return array<string, Facility> */
    private function seedFacilities(): array
    {
        $hospital = $this->saveFacility('Al-Nour Teaching Hospital', [
            'parent_id' => null, 'facility_type' => 'hospital', 'phone_number' => '+966112000001',
            'address' => 'Riyadh, Al-Nour District', 'is_active' => true,
        ]);

        $facilities = [
            'hospital' => $hospital,
            'clinic' => $this->saveFacility('Al-Nour Family Clinic', [
                'parent_id' => $hospital->id, 'facility_type' => 'clinic', 'phone_number' => '+966112000002',
                'address' => 'Riyadh, Al-Nour District', 'is_active' => true,
            ]),
            'pharmacy' => $this->saveFacility('Al-Nour Community Pharmacy', [
                'parent_id' => $hospital->id, 'facility_type' => 'pharmacy', 'phone_number' => '+966112000003',
                'address' => 'Riyadh, Al-Nour District', 'is_active' => true,
            ]),
            'laboratory' => $this->saveFacility('Al-Nour Diagnostic Laboratory', [
                'parent_id' => $hospital->id, 'facility_type' => 'laboratory', 'phone_number' => '+966112000004',
                'address' => 'Riyadh, Al-Nour District', 'is_active' => true,
            ]),
            'inactive_clinic' => $this->saveFacility('Al-Nour Archived Clinic', [
                'parent_id' => $hospital->id, 'facility_type' => 'clinic', 'phone_number' => '+966112000005',
                'address' => 'Riyadh, Al-Nour District', 'is_active' => false,
            ]),
        ];

        for ($index = 6; $index <= max(5, (int) config('healthcare_seed.facilities', 5)); $index++) {
            $facilities["satellite_{$index}"] = $this->saveFacility("Al-Nour Satellite Clinic {$index}", [
                'parent_id' => $hospital->id,
                'facility_type' => 'clinic',
                'phone_number' => sprintf('+966112%06d', $index),
                'address' => "Riyadh, Satellite District {$index}",
                'is_active' => true,
            ]);
        }

        return $facilities;
    }

    /** @return array<string, Department> */
    private function seedDepartments(): array
    {
        $data = [
            'general' => ['General Medicine', 'General outpatient assessment and follow-up.', true],
            'cardiology' => ['Cardiology', 'Cardiovascular assessment and treatment.', true],
            'pediatrics' => ['Pediatrics', 'Child health assessment and treatment.', true],
            'laboratory' => ['Laboratory Services', 'Diagnostic laboratory services.', true],
            'retired' => ['Retired Department', 'Reference record retained for historical configuration.', false],
        ];

        $result = [];
        foreach ($data as $key => [$name, $description, $isActive]) {
            $department = Department::query()->firstOrNew(['name' => $name]);
            $department->forceFill(['description' => $description, 'is_active' => $isActive])->save();
            $result[$key] = $department;
        }

        return $result;
    }

    /** @return array<string, Specialization> */
    private function seedSpecializations(): array
    {
        $data = [
            'family' => ['Family Medicine', 'Primary and continuing care.', true],
            'cardiology' => ['Cardiology', 'Heart and vascular medicine.', true],
            'pediatrics' => ['Pediatrics', 'Medical care for children.', true],
            'dermatology' => ['Dermatology', 'Skin, hair, and nail medicine.', true],
            'retired' => ['Retired Specialization', 'Inactive reference specialization.', false],
        ];

        $result = [];
        foreach ($data as $key => [$name, $description, $isActive]) {
            $specialization = Specialization::query()->firstOrNew(['name' => $name]);
            $specialization->forceFill(['description' => $description, 'is_active' => $isActive])->save();
            $result[$key] = $specialization;
        }

        return $result;
    }

    /** @param array<string, Facility> $facilities @param array<string, Department> $departments @param array<string, Specialization> $specializations */
    private function seedFacilityConfiguration(array $facilities, array $departments, array $specializations): void
    {
        $assignments = [
            ['hospital', 'general', 'family', true],
            ['hospital', 'cardiology', 'cardiology', true],
            ['hospital', 'pediatrics', 'pediatrics', true],
            ['hospital', 'general', 'dermatology', true],
            ['clinic', 'general', 'family', true],
            ['clinic', 'pediatrics', 'pediatrics', true],
            ['clinic', 'general', 'dermatology', true],
            ['hospital', 'retired', 'retired', false],
        ];

        foreach ($assignments as [$facilityKey, $departmentKey, $specializationKey, $isActive]) {
            $facilityDepartment = FacilityDepartment::query()->firstOrCreate([
                'facility_id' => $facilities[$facilityKey]->id,
                'department_id' => $departments[$departmentKey]->id,
            ]);
            $assignment = FacilityDepartmentSpecialization::query()->firstOrNew([
                'facility_department_id' => $facilityDepartment->id,
                'specialization_id' => $specializations[$specializationKey]->id,
            ]);
            $assignment->forceFill(['is_active' => $isActive])->save();
        }

        foreach ($facilities as $facility) {
            if ($facility->facility_type !== 'clinic' || ! $facility->is_active) {
                continue;
            }
            $facilityDepartment = FacilityDepartment::query()->firstOrCreate([
                'facility_id' => $facility->id,
                'department_id' => $departments['general']->id,
            ]);
            FacilityDepartmentSpecialization::query()->updateOrCreate([
                'facility_department_id' => $facilityDepartment->id,
                'specialization_id' => $specializations['family']->id,
            ], ['is_active' => true]);
        }
    }

    private function seedLabTests(): void
    {
        foreach ([
            ['Complete Blood Count', 4.50, 10.50, 'x10^9/L'],
            ['Fasting Blood Glucose', 70.00, 99.00, 'mg/dL'],
            ['Total Cholesterol', 125.00, 200.00, 'mg/dL'],
            ['Thyroid Stimulating Hormone', 0.40, 4.50, 'mIU/L'],
            ['Serum Creatinine', 0.60, 1.30, 'mg/dL'],
        ] as [$name, $low, $high, $unit]) {
            LabTest::query()->updateOrCreate(['name' => $name], [
                'range_low' => $low, 'range_high' => $high, 'unit' => $unit,
            ]);
        }
    }

    private function seedMedicalConditions(): void
    {
        foreach ([
            ['Type 2 Diabetes Mellitus', 'chronic', 'Chronic glucose metabolism condition.'],
            ['Essential Hypertension', 'chronic', 'Chronic high blood pressure.'],
            ['Asthma', 'chronic', null],
            ['Penicillin', 'allergy', 'Antibiotic allergy.'],
            ['Peanuts', 'allergy', 'Food allergy.'],
        ] as [$name, $type, $notes]) {
            MedicalCondition::query()->updateOrCreate(['name' => $name, 'type' => $type], ['notes' => $notes]);
        }
    }

    private function saveFacility(string $name, array $attributes): Facility
    {
        $facility = Facility::query()->firstOrNew(['name' => $name]);
        $facility->forceFill($attributes)->save();

        return $facility;
    }
}
