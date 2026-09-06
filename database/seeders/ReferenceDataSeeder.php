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

        $this->seedFacilityConfiguration(
            $facilities,
            $departments,
            $specializations
        );

        $this->seedLabTests();
        $this->seedMedicalConditions();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function seedRolesAndPermissions(): void
    {
        $permissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            'view_profiles',
            'edit_profiles',

            'view_facilities',
            'create_facilities',
            'edit_facilities',
            'delete_facilities',

            'view_doctors',
            'create_doctors',
            'edit_doctors',
            'delete_doctors',

            'manage_specializations',

            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'cancel_appointments',
            'manage_doctor_schedules',

            'view_medical_records',
            'create_medical_records',
            'edit_medical_records',
            'view_patient_history',

            'view_prescriptions',
            'create_prescriptions',
            'edit_prescriptions',
            'dispense_prescriptions',

            'view_lab_requests',
            'create_lab_requests',
            'upload_lab_results',
            'approve_lab_results',

            'manage_allergies',
            'manage_chronic_diseases',
        ];

        foreach ($permissions as $name) {
            Permission::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        foreach ([
            'admin',
            'manager',
            'doctor',
            'laboratory',
            'pharmacist',
            'patient',
        ] as $name) {
            Role::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $all = Permission::query()
            ->where('guard_name', 'web')
            ->get();

        Role::findByName('admin')
            ->syncPermissions($all);

        Role::findByName('manager')
            ->syncPermissions(
                $all->reject(
                    fn (Permission $permission) =>
                        str_starts_with($permission->name, 'delete_')
                )
            );

        Role::findByName('doctor')
            ->syncPermissions(
                $all->filter(
                    fn (Permission $permission) =>
                        str_contains($permission->name, 'doctor')
                        || str_contains($permission->name, 'appointment')
                        || str_contains($permission->name, 'medical')
                        || str_contains($permission->name, 'prescription')
                        || str_contains($permission->name, 'lab_request')
                        || $permission->name === 'view_patient_history'
                )
            );

        Role::findByName('laboratory')
            ->syncPermissions(
                $all->filter(
                    fn (Permission $permission) =>
                        str_contains($permission->name, 'lab_')
                        || str_contains($permission->name, 'medical_record')
                )
            );

        Role::findByName('pharmacist')
            ->syncPermissions(
                $all->filter(
                    fn (Permission $permission) =>
                        str_contains($permission->name, 'prescription')
                )
            );

        Role::findByName('patient')
            ->syncPermissions(
                $all->filter(
                    fn (Permission $permission) =>
                        str_contains($permission->name, 'appointment')
                        || in_array(
                            $permission->name,
                            [
                                'view_patient_history',
                                'view_prescriptions',
                                'view_lab_requests',
                            ],
                            true
                        )
                )
            );
    }

    private function seedFacilities(): array
    {
        $hospital = $this->saveFacility(
            'مجمع الشفاء الطبي',
            [
                'parent_id' => null,
                'facility_type' => 'hospital',
                'phone_number' => '+963 11 000 0001',
                'address' => 'دمشق، المزة',
                'is_active' => true,
            ]
        );

        $facilities = [
            'hospital' => $hospital,

            'clinic' => $this->saveFacility(
                'مركز الياسمين للرعاية الصحية',
                [
                    'parent_id' => $hospital->id,
                    'facility_type' => 'clinic',
                    'phone_number' => '+963 11 000 0002',
                    'address' => 'دمشق، أبو رمانة',
                    'is_active' => true,
                ]
            ),

            'pharmacy' => $this->saveFacility(
                'صيدلية الرحمة المركزية',
                [
                    'parent_id' => $hospital->id,
                    'facility_type' => 'pharmacy',
                    'phone_number' => '+963 11 000 0003',
                    'address' => 'دمشق، المزة',
                    'is_active' => true,
                ]
            ),

            'laboratory' => $this->saveFacility(
                'مختبر الأمل التشخيصي',
                [
                    'parent_id' => $hospital->id,
                    'facility_type' => 'laboratory',
                    'phone_number' => '+963 11 000 0004',
                    'address' => 'دمشق، المزة',
                    'is_active' => true,
                ]
            ),

            'inactive_clinic' => $this->saveFacility(
                'مركز الندى الطبي',
                [
                    'parent_id' => $hospital->id,
                    'facility_type' => 'clinic',
                    'phone_number' => '+963 11 000 0005',
                    'address' => 'دمشق، الميدان',
                    'is_active' => false,
                ]
            ),
        ];

        $facilityCount = max(
            5,
            (int) config('healthcare_seed.facilities', 5)
        );

        $districts = [
            'جرمانا',
            'كفرسوسة',
            'المهاجرين',
            'ركن الدين',
            'القصاع',
            'الميدان',
            'الزاهرة',
            'برزة',
            'دمر',
            'قدسيا',
        ];

        for ($index = 6; $index <= $facilityCount; $index++) {
            $districtIndex = ($index - 6) % count($districts);
            $branchNumber = intdiv($index - 6, count($districts)) + 1;

            $district = $districts[$districtIndex];

            $suffix = $branchNumber > 1
                ? " - الفرع {$branchNumber}"
                : '';

            $facilities["clinic_{$index}"] = $this->saveFacility(
                "مركز {$district} للرعاية الصحية{$suffix}",
                [
                    'parent_id' => $hospital->id,
                    'facility_type' => 'clinic',
                    'phone_number' => sprintf(
                        '+963 11 000 %04d',
                        $index
                    ),
                    'address' => "دمشق، {$district}",
                    'is_active' => true,
                ]
            );
        }

        return $facilities;
    }

    private function seedDepartments(): array
    {
        $data = [
            'general' => [
                'الطب العام',
                'خدمات المعاينة الطبية العامة والرعاية الأولية.',
            ],

            'internal' => [
                'الأمراض الباطنية',
                'تشخيص ومتابعة الأمراض الداخلية المزمنة والحادة.',
            ],

            'cardiology' => [
                'أمراض القلب',
                'تشخيص ومتابعة أمراض القلب والأوعية الدموية.',
            ],

            'pediatrics' => [
                'طب الأطفال',
                'الرعاية الطبية للأطفال ومتابعة نموهم وصحتهم.',
            ],

            'dermatology' => [
                'الأمراض الجلدية',
                'تشخيص ومعالجة أمراض الجلد والشعر والأظافر.',
            ],

            'gynecology' => [
                'النسائية والتوليد',
                'الرعاية الصحية للنساء وخدمات متابعة الحمل.',
            ],

            'surgery' => [
                'الجراحة العامة',
                'التقييم الجراحي والإجراءات الجراحية العامة.',
            ],
        ];

        $departments = [];

        foreach ($data as $key => [$name, $description]) {
            $departments[$key] = Department::query()->updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        return $departments;
    }

    private function seedSpecializations(): array
    {
        $data = [
            'family' => [
                'طب الأسرة',
                'الرعاية الطبية الأولية والمستمرة لجميع أفراد الأسرة.',
            ],

            'internal' => [
                'الطب الباطني',
                'تشخيص ومتابعة الأمراض الباطنية.',
            ],

            'cardiology' => [
                'أمراض القلب',
                'اختصاص تشخيص وعلاج أمراض القلب والأوعية.',
            ],

            'pediatrics' => [
                'طب الأطفال',
                'الرعاية الطبية للأطفال والمراهقين.',
            ],

            'dermatology' => [
                'الأمراض الجلدية',
                'تشخيص ومعالجة الأمراض الجلدية.',
            ],

            'gynecology' => [
                'النسائية والتوليد',
                'الرعاية الصحية النسائية ومتابعة الحمل والولادة.',
            ],

            'general_surgery' => [
                'الجراحة العامة',
                'تشخيص الحالات التي تحتاج إلى تدخل جراحي.',
            ],
        ];

        $specializations = [];

        foreach ($data as $key => [$name, $description]) {
            $specializations[$key] = Specialization::query()->updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        return $specializations;
    }

    private function seedFacilityConfiguration(
        array $facilities,
        array $departments,
        array $specializations
    ): void {
        $assignments = [
            ['hospital', 'general', 'family', true],
            ['hospital', 'internal', 'internal', true],
            ['hospital', 'cardiology', 'cardiology', true],
            ['hospital', 'pediatrics', 'pediatrics', true],
            ['hospital', 'dermatology', 'dermatology', true],
            ['hospital', 'gynecology', 'gynecology', true],
            ['hospital', 'surgery', 'general_surgery', true],

            ['clinic', 'general', 'family', true],
            ['clinic', 'internal', 'internal', true],
            ['clinic', 'pediatrics', 'pediatrics', true],
            ['clinic', 'dermatology', 'dermatology', true],

            // Inactive configuration used to test business rules.
            ['clinic', 'cardiology', 'cardiology', false],
        ];

        foreach ($assignments as [
            $facilityKey,
            $departmentKey,
            $specializationKey,
            $isActive,
        ]) {
            $this->saveFacilitySpecialization(
                $facilities[$facilityKey],
                $departments[$departmentKey],
                $specializations[$specializationKey],
                $isActive
            );
        }

        foreach ($facilities as $key => $facility) {
            if (
                $facility->is_active
                && $facility->facility_type === 'clinic'
            ) {
                $facilityDepartment = FacilityDepartment::query()
                    ->firstOrCreate([
                        'facility_id' => $facility->id,
                        'department_id' => $departments['general']->id,
                    ]);

                FacilityDepartmentSpecialization::query()
                    ->updateOrCreate(
                        [
                            'facility_department_id' => $facilityDepartment->id,
                            'specialization_id' => $specializations['family']->id,
                        ],
                        [
                            'is_active' => true,
                        ]
                    );
            }
        }
    }

    private function saveFacilitySpecialization(
        Facility $facility,
        Department $department,
        Specialization $specialization,
        bool $isActive
    ): FacilityDepartmentSpecialization {
        $facilityDepartment = FacilityDepartment::query()
            ->firstOrCreate([
                'facility_id' => $facility->id,
                'department_id' => $department->id,
            ]);

        return FacilityDepartmentSpecialization::query()
            ->updateOrCreate(
                [
                    'facility_department_id' => $facilityDepartment->id,
                    'specialization_id' => $specialization->id,
                ],
                [
                    'is_active' => $isActive,
                ]
            );
    }

    private function seedLabTests(): void
    {
        $tests = [
            [
                'عدد كريات الدم البيضاء',
                4.0,
                10.0,
                'x10^9/L',
            ],

            [
                'الهيموغلوبين',
                12.0,
                17.0,
                'g/dL',
            ],

            [
                'سكر الدم الصيامي',
                70.0,
                99.0,
                'mg/dL',
            ],

            [
                'الهيموغلوبين السكري',
                4.0,
                5.6,
                '%',
            ],

            [
                'الكوليسترول الكلي',
                125.0,
                200.0,
                'mg/dL',
            ],

            [
                'الكرياتينين',
                0.6,
                1.3,
                'mg/dL',
            ],

            [
                'الهرمون المنبه للدرق',
                0.4,
                4.0,
                'mIU/L',
            ],
        ];

        foreach ($tests as [
            $name,
            $normalMin,
            $normalMax,
            $unit,
        ]) {
            LabTest::query()->updateOrCreate(
                ['name' => $name],
                [
                    'range_low' => $normalMin,
                    'range_high' => $normalMax,
                    'unit' => $unit,
                ]
            );
        }
    }

    private function seedMedicalConditions(): void
    {
        $conditions = [
            [
                'داء السكري من النوع الثاني',
                'chronic',
                'اضطراب مزمن يؤثر في تنظيم مستوى سكر الدم.',
            ],

            [
                'ارتفاع ضغط الدم',
                'chronic',
                'ارتفاع مستمر في ضغط الدم يحتاج إلى المتابعة.',
            ],

            [
                'الربو',
                'chronic',
                'مرض مزمن يؤثر في الشعب الهوائية والتنفس.',
            ],

            [
                'ارتفاع الكوليسترول',
                'chronic',
                'ارتفاع مستويات الكوليسترول في الدم.',
            ],

            [
                'الصداع النصفي',
                'chronic',
                'حالة متكررة تسبب نوبات من الصداع.',
            ],

            [
                'حساسية البنسلين',
                'allergy',
                'تحسس دوائي تجاه مجموعة البنسلين.',
            ],

            [
                'حساسية مضادات الالتهاب غير الستيرويدية',
                'allergy',
                'تحسس تجاه بعض مضادات الالتهاب غير الستيرويدية.',
            ],

            [
                'حساسية الفول السوداني',
                'allergy',
                'حساسية تجاه الفول السوداني ومشتقاته.',
            ],
        ];

        foreach ($conditions as [
            $name,
            $type,
            $description,
        ]) {
            MedicalCondition::query()->updateOrCreate(
                ['name' => $name, 'type' => $type],
                [
                    'notes' => $description,
                ]
            );
        }
    }

    private function saveFacility(
        string $name,
        array $data
    ): Facility {
        return Facility::query()->updateOrCreate(
            ['name' => $name],
            $data
        );
    }
}

