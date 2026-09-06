<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\FacilityDepartmentSpecialization;
use App\Models\LabRequestItem;
use App\Models\LabStaff;
use App\Models\LabTest;
use App\Models\MedicalCondition;
use App\Models\Patient;
use App\Models\PatientMedicalCondition;
use App\Models\Pharmacist;
use App\Models\Profile;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\DoctorService;
use App\Services\LabRequestItemService;
use App\Services\PrescriptionItemService;
use App\Services\PrescriptionService;
use App\Services\VisitService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HealthcareScenarioSeeder extends Seeder
{
    public function __construct(
        protected AppointmentService $appointmentService,
        protected VisitService $visitService,
        protected PrescriptionService $prescriptionService,
        protected PrescriptionItemService $prescriptionItemService,
        protected LabRequestItemService $labRequestService,
    ) {
    }

    public function run(): void
    {
        if (Facility::query()->count() === 0) {
            $this->call(ReferenceDataSeeder::class);
        }

        $this->seedAdministratorsAndManagers();
        $this->seedProfessionals();
        $this->seedPatients();
        $this->seedPatientMedicalConditions();
        $this->seedSchedules();
        $this->seedClinicalScenarios();
    }

    private function seedAdministratorsAndManagers(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@seed.local'],
            [
                'name' => 'عبد الرحمن خالد الحسن',
                'password' => Hash::make(
                    config('healthcare_seed.password')
                ),
                'is_active' => true,
            ]
        );

        $admin->syncRoles(['admin']);

        $this->person(
            email: 'admin@seed.local',
            fullName: 'عبد الرحمن خالد الحسن',
            sequence: 1,
            active: true,
            gender: 'male'
        );

        $managerData = [
            [
                'email' => 'manager.hospital@seed.local',
                'name' => 'سامر فؤاد الخطيب',
                'gender' => 'male',
                'facility' => 'مجمع الشفاء الطبي',
            ],

            [
                'email' => 'manager.clinic@seed.local',
                'name' => 'سارة ناصر العلي',
                'gender' => 'female',
                'facility' => 'مركز الياسمين للرعاية الصحية',
            ],

            [
                'email' => 'manager.pharmacy@seed.local',
                'name' => 'ريم مازن الدروبي',
                'gender' => 'female',
                'facility' => 'صيدلية الرحمة المركزية',
            ],

            [
                'email' => 'manager.laboratory@seed.local',
                'name' => 'ياسر محمود النجار',
                'gender' => 'male',
                'facility' => 'مختبر الأمل التشخيصي',
            ],
        ];

        foreach ($managerData as $index => $manager) {
            $facility = Facility::query()
                ->where('name', $manager['facility'])
                ->firstOrFail();

            [$user, $profile] = $this->person(
                email: $manager['email'],
                fullName: $manager['name'],
                sequence: 10 + $index,
                active: true,
                gender: $manager['gender']
            );

            $user->syncRoles(['manager']);

            Employee::query()->updateOrCreate(
                ['profile_id' => $profile->id],
                [
                    'facility_id' => $facility->id,
                    'languages' => ['Arabic', 'English'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedProfessionals(): void
    {
        $doctorAssignments = FacilityDepartmentSpecialization::query()
            ->with([
                'specialization',
                'facilityDepartment.facility',
                'facilityDepartment.department',
            ])
            ->where('is_active', true)
            ->get();

        if ($doctorAssignments->isEmpty()) {
            throw new \RuntimeException(
                'No active facility specialization assignments were found.'
            );
        }

        $doctorCount = max(
            1,
            (int) config('healthcare_seed.doctors', 10)
        );

        for ($index = 1; $index <= $doctorCount; $index++) {
            $sequence = 1000 + $index;

            [$name, $gender] = $this->arabicName($sequence);

            $assignment = $doctorAssignments[
                ($index - 1) % $doctorAssignments->count()
            ];

            $facility = $assignment
                ->facilityDepartment
                ->facility;

            $department = $assignment
                ->facilityDepartment
                ->department;

            $specialization = $assignment->specialization;

            [$user, $profile] = $this->person(
                email: "doctor.{$index}@seed.local",
                fullName: $name,
                sequence: $sequence,
                active: true,
                gender: $gender
            );

            $user->syncRoles(['doctor']);

            $employee = Employee::query()->updateOrCreate(
                ['profile_id' => $profile->id],
                [
                    'facility_id' => $facility->id,
                    'languages' => ['Arabic', 'English'],
                    'is_active' => true,
                ]
            );

            Doctor::query()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'facility_department_specialization_id' =>
                        $assignment->id,
                    'qualification' =>
                        $this->doctorQualification($index),
                    'years_of_experience' =>
                        3 + (($index * 2) % 18),
                    'biography' =>
                        "طبيب متخصص في {$specialization->name} "
                        . "يعمل ضمن قسم {$department->name} "
                        . "في {$facility->name}.",
                    'achievements' =>
                        $this->doctorAchievement($index),
                ]
            );
        }

        $pharmacistCount = max(
            1,
            (int) config('healthcare_seed.pharmacists', 10)
        );

        $pharmacy = Facility::query()
            ->where('facility_type', 'pharmacy')
            ->firstOrFail();

        for ($index = 1; $index <= $pharmacistCount; $index++) {
            $sequence = 2000 + $index;

            [$name, $gender] = $this->arabicName($sequence);

            [$user, $profile] = $this->person(
                email: "pharmacist.{$index}@seed.local",
                fullName: $name,
                sequence: $sequence,
                active: true,
                gender: $gender
            );

            $user->syncRoles(['pharmacist']);

            $employee = Employee::query()->updateOrCreate(
                ['profile_id' => $profile->id],
                [
                    'facility_id' => $pharmacy->id,
                    'languages' => ['Arabic', 'English'],
                    'is_active' => true,
                ]
            );

            Pharmacist::query()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'degree' =>
                        $index % 2 === 0
                            ? 'دكتور صيدلة'
                            : 'بكالوريوس صيدلة',
                    'years_of_experience' =>
                        2 + (($index * 2) % 15),
                    'license_number' =>
                        sprintf('SY-PH-%05d', $index),
                ]
            );
        }

        $labStaffCount = max(
            1,
            (int) config('healthcare_seed.lab_staff', 10)
        );

        $laboratory = Facility::query()
            ->where('facility_type', 'laboratory')
            ->firstOrFail();

        $labSpecializations = [
            'أمراض الدم',
            'الكيمياء السريرية',
            'الأحياء الدقيقة',
            'المناعة',
        ];

        for ($index = 1; $index <= $labStaffCount; $index++) {
            $sequence = 3000 + $index;

            [$name, $gender] = $this->arabicName($sequence);

            [$user, $profile] = $this->person(
                email: "laboratory.{$index}@seed.local",
                fullName: $name,
                sequence: $sequence,
                active: true,
                gender: $gender
            );

            $user->syncRoles(['laboratory']);

            $employee = Employee::query()->updateOrCreate(
                ['profile_id' => $profile->id],
                [
                    'facility_id' => $laboratory->id,
                    'languages' => ['Arabic', 'English'],
                    'is_active' => true,
                ]
            );

            LabStaff::query()->updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'specialization' =>
                        $labSpecializations[
                            ($index - 1) % count($labSpecializations)
                        ],
                    'degree' => 'بكالوريوس علوم مخبرية طبية',
                    'years_of_experience' =>
                        2 + (($index * 2) % 14),
                    'license_number' =>
                        sprintf('SY-LAB-%05d', $index),
                ]
            );
        }

        $this->deactivateLastProfessional(
            'doctor.'.$doctorCount.'@seed.local'
        );

        $this->deactivateLastProfessional(
            'pharmacist.'.$pharmacistCount.'@seed.local'
        );

        $this->deactivateLastProfessional(
            'laboratory.'.$labStaffCount.'@seed.local'
        );
    }

    private function seedPatients(): void
    {
        $patientCount = max(
            1,
            (int) config('healthcare_seed.patients', 100)
        );

        for ($index = 1; $index <= $patientCount; $index++) {
            $sequence = 100000 + $index;

            [$name, $gender] = $this->arabicName($sequence);

            $active = $patientCount === 1 || $index !== $patientCount;

            [$user, $profile] = $this->person(
                email: "patient.{$index}@seed.local",
                fullName: $name,
                sequence: $sequence,
                active: $active,
                gender: $gender
            );

            $user->syncRoles(['patient']);

            Patient::query()->updateOrCreate(
                ['profile_id' => $profile->id],
                [
                    'blood_type' => $this->bloodType($index),
                    'emergency_contact_name' =>
                        $this->emergencyContactName($index),
                    'emergency_contact_phone' =>
                        $this->emergencyContactPhone($index),
                ]
            );
        }
    }

    private function seedPatientMedicalConditions(): void
    {
        $conditions = MedicalCondition::query()
            ->orderBy('id')
            ->get();

        if ($conditions->isEmpty()) {
            return;
        }

        $patients = Patient::query()
            ->with('profile')
            ->whereHas(
                'profile.user',
                fn ($query) =>
                    $query->where(
                        'email',
                        'like',
                        'patient.%@seed.local'
                    )
            )
            ->orderBy('id')
            ->get();

        foreach ($patients as $index => $patient) {
            $sequence = $index + 1;

            // Every fourth patient has no recorded condition.
            if ($sequence % 4 === 0) {
                continue;
            }

            $condition = $conditions[
                ($sequence - 1) % $conditions->count()
            ];

            PatientMedicalCondition::query()->updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'medical_condition_id' => $condition->id,
                ],
                [
                    'diagnosed_at' => now()->subMonths(
                        3 + ($sequence % 30)
                    ),
                    'notes' =>
                        'حالة مسجلة ضمن السجل الطبي للمريض '
                        . 'وتحتاج إلى المتابعة حسب تقييم الطبيب.',
                ]
            );

            // Some patients have more than one condition.
            if ($sequence % 5 === 0 && $conditions->count() > 1) {
                $secondCondition = $conditions[
                    $sequence % $conditions->count()
                ];

                if ($secondCondition->id !== $condition->id) {
                    PatientMedicalCondition::query()->updateOrCreate(
                        [
                            'patient_id' => $patient->id,
                            'medical_condition_id' =>
                                $secondCondition->id,
                        ],
                        [
                            'diagnosed_at' => now()->subMonths(
                                6 + ($sequence % 24)
                            ),
                            'notes' =>
                                'حالة إضافية مسجلة في الملف الطبي.',
                        ]
                    );
                }
            }
        }
    }

    private function seedSchedules(): void
    {
        $doctors = Doctor::query()
            ->whereHas('employee', fn ($query) =>
                $query->where('is_active', true)
            )
            ->get();

        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];

        foreach ($doctors as $doctor) {
            foreach ($days as $day) {
                DoctorSchedule::query()->updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'is_off' => $day === 'Sunday',
                        'start_time' => '08:00:00',
                        'end_time' => '16:00:00',
                        'avg_consultation_time' => 30,
                    ]
                );
            }
        }
    }

    private function seedClinicalScenarios(): void
    {
        $doctors = Doctor::query()
            ->with('employee')
            ->whereHas('employee', fn ($query) =>
                $query->where('is_active', true)
            )
            ->get();

        if ($doctors->isEmpty()) {
            return;
        }

        $pharmacist = Pharmacist::query()
            ->with('employee')
            ->whereHas('employee', fn ($query) =>
                $query->where('is_active', true)
            )
            ->firstOrFail();

        $labStaff = LabStaff::query()
            ->with('employee')
            ->whereHas('employee', fn ($query) =>
                $query->where('is_active', true)
            )
            ->firstOrFail();

        $transactionPatientCount = max(
            1,
            (int) config(
                'healthcare_seed.transaction_patients',
                48
            )
        );

        $patients = Patient::query()
            ->with('profile')
            ->whereHas(
                'profile.user',
                fn ($query) =>
                    $query
                        ->where(
                            'email',
                            'like',
                            'patient.%@seed.local'
                        )
                        ->where('is_active', true)
            )
            ->orderBy('id')
            ->limit($transactionPatientCount)
            ->get();

        foreach ($patients as $index => $patient) {
            $doctor = $doctors[
                $index % $doctors->count()
            ];

            $scenario = $index % 4;

            if ($scenario < 2) {
                $appointment = $this->seedHistoricalAppointment(
                    $patient,
                    $doctor,
                    $index,
                    $scenario
                );

                $visit = $this->visitService
                    ->startVisit($appointment->id);

                $this->seedActiveVisitRecords(
                    $visit->id,
                    $patient,
                    $doctor,
                    $pharmacist,
                    $labStaff,
                    $scenario
                );

                if ($scenario === 0) {
                    $this->visitService->completeVisit(
                        $visit->id,
                        [
                            'notes' =>
                                'تمت المعاينة الطبية وإغلاق الزيارة '
                                . 'بعد استكمال الإجراءات المطلوبة.',
                        ]
                    );
                }

                continue;
            }

            $this->seedFutureAppointment(
                $patient,
                $doctor,
                $index,
                $scenario === 3
            );
        }
    }

    private function seedHistoricalAppointment(
        Patient $patient,
        Doctor $doctor,
        int $index,
        int $scenario
    ): Appointment {
        $date = now()
            ->subDays(7 + $index)
            ->startOfDay();

        $date = $date->isSunday()
            ? $date->subDay()
            : $date;

        $time = sprintf(
            '%02d:%02d:00',
            8 + ($index % 7),
            ($index % 2) * 30
        );

        return Appointment::query()->updateOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_date' => $date->toDateString(),
                'start_time' => $time,
            ],
            [
                'status' => 'confirmed',
                'reason' =>
                    $scenario === 0
                        ? 'متابعة دورية للحالة الصحية.'
                        : 'مراجعة طبية واستكمال الخطة العلاجية.',
            ]
        );
    }

    private function seedFutureAppointment(
        Patient $patient,
        Doctor $doctor,
        int $index,
        bool $cancelled
    ): Appointment {
        $date = now()
            ->addDays(3 + $index)
            ->startOfDay();

        while ($date->isSunday()) {
            $date->addDay();
        }

        $time = sprintf(
            '%02d:%02d:00',
            9 + ($index % 6),
            ($index % 2) * 30
        );

        return Appointment::query()->updateOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_date' => $date->toDateString(),
                'start_time' => $time,
            ],
            [
                'status' => $cancelled
                    ? 'cancelled'
                    : 'pending',
                'reason' =>
                    $cancelled
                        ? 'تم إلغاء الموعد من قبل المريض.'
                        : 'موعد لمراجعة الحالة الطبية.',
            ]
        );
    }

    private function seedActiveVisitRecords(
        int $visitId,
        Patient $patient,
        Doctor $doctor,
        Pharmacist $pharmacist,
        LabStaff $labStaff,
        int $scenario
    ): void {
        $diagnoses = [
            [
                'code' => 'I10',
                'description' => 'ارتفاع ضغط الدم الأساسي.',
            ],

            [
                'code' => 'E11',
                'description' =>
                    'داء السكري من النوع الثاني.',
            ],

            [
                'code' => 'J45',
                'description' => 'الربو.',
            ],

            [
                'code' => 'L20',
                'description' =>
                    'التهاب الجلد التأتبي.',
            ],
        ];

        $diagnosis = $diagnoses[
            $patient->id % count($diagnoses)
        ];

        $this->createDiagnosis(
            $visitId,
            $diagnosis['code'],
            $diagnosis['description'],
            'primary'
        );

        if ($scenario === 0) {
            $this->createDiagnosis(
                $visitId,
                'Z09',
                'فحص متابعة بعد العلاج.',
                'secondary'
            );
        }

        $prescription = $this->prescriptionService
            ->createPrescription(
                [
                    'visit_id' => $visitId,
                    'status' => 'pending',
                    'notes' =>
                        'وصفة دوائية حسب الخطة العلاجية للطبيب.',
                ]
            );

        $item1 = $this->prescriptionItemService
            ->createPrescriptionItem(
                [
                    'prescription_id' => $prescription->id,
                    'medication_name' => 'باراسيتامول',
                    'dosage' => '500 mg',
                    'quantity_prescribed' => 20,
                    'frequency' => 'مرتين يومياً',
                    'duration' => '5 أيام',
                ]
            );

        $item2 = $this->prescriptionItemService
            ->createPrescriptionItem(
                [
                    'prescription_id' => $prescription->id,
                    'medication_name' => 'أوميبرازول',
                    'dosage' => '20 mg',
                    'quantity_prescribed' => 14,
                    'frequency' => 'مرة يومياً',
                    'duration' => '14 يوماً',
                ]
            );

        $this->seedDispensingScenario(
            $item1->id,
            $item2->id,
            $pharmacist,
            $scenario
        );

        $labTests = LabTest::query()
            ->orderBy('id')
            ->limit(2)
            ->get();

        foreach ($labTests as $testIndex => $labTest) {
            $requestItem = $this->labRequestService
                ->createLabRequestItem(
                    [
                        'visit_id' => $visitId,
                        'lab_test_id' => $labTest->id,
                        'notes' =>
                            'طلب فحص مخبري ضمن الخطة التشخيصية.',
                    ]
                );

            $this->seedLabScenario(
                $requestItem,
                $labStaff,
                $labTest,
                $scenario,
                $testIndex
            );
        }
    }

    private function seedDispensingScenario(
        int $item1Id,
        int $item2Id,
        Pharmacist $pharmacist,
        int $scenario
    ): void {
        if ($scenario === 3) {
            return;
        }

        if ($scenario === 1) {
            DB::table('dispensings')->updateOrInsert(
                [
                    'prescription_item_id' => $item1Id,
                    'pharmacist_id' => $pharmacist->id,
                ],
                [
                    'quantity_dispensed' => 10,
                    'dispensed_at' => now()->subDays(2),
                ]
            );

            return;
        }

        DB::table('dispensings')->updateOrInsert(
            [
                'prescription_item_id' => $item1Id,
                'pharmacist_id' => $pharmacist->id,
            ],
            [
                'quantity_dispensed' => 20,
                'dispensed_at' => now()->subDays(2),
            ]
        );

        DB::table('dispensings')->updateOrInsert(
            [
                'prescription_item_id' => $item2Id,
                'pharmacist_id' => $pharmacist->id,
            ],
            [
                'quantity_dispensed' => 14,
                'dispensed_at' => now()->subDays(2),
            ]
        );
    }

    private function seedLabScenario(
        LabRequestItem $requestItem,
        LabStaff $labStaff,
        LabTest $labTest,
        int $scenario,
        int $testIndex
    ): void {
        if ($scenario === 3) {
            return;
        }

        if ($scenario === 1) {
            $this->setLabRequestStatus($requestItem->id, 'processing');

            DB::table('lab_results')->updateOrInsert(
                [
                    'lab_request_item_id' => $requestItem->id,
                ],
                [
                    'lab_staff_id' => $labStaff->id,
                    'value' => 0,
                    'unit' => $labTest->unit,
                    'reference_range' =>
                        "{$labTest->range_low}-"
                        . "{$labTest->range_high}",
                    'notes' =>
                        'الفحص قيد المعالجة في المختبر.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return;
        }

        $this->setLabRequestStatus($requestItem->id, 'completed');

        $value = (
            $labTest->range_low
            + $labTest->range_high
        ) / 2;

        DB::table('lab_results')->updateOrInsert(
            [
                'lab_request_item_id' => $requestItem->id,
            ],
            [
                'lab_staff_id' => $labStaff->id,
                'value' => $value,
                'unit' => $labTest->unit,
                'reference_range' =>
                    "{$labTest->range_low}-"
                    . "{$labTest->range_high}",
                'notes' =>
                    $testIndex === 0
                        ? 'النتيجة ضمن المجال المرجعي.'
                        : 'تم إصدار النتيجة بعد استكمال الفحص.',
                'completed_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function setLabRequestStatus(int $requestItemId, string $status): void
    {
        LabRequestItem::query()
            ->whereKey($requestItemId)
            ->update(['status' => $status]);
    }

    private function createDiagnosis(
        int $visitId,
        string $code,
        string $description,
        string $type
    ): void {
        DB::table('diagnoses')->updateOrInsert(
            [
                'visit_id' => $visitId,
                'diagnosis_code' => $code,
            ],
            [
                'description' => $description,
                'diagnosis_type' => $type,
                'notes' =>
                    'تم تسجيل التشخيص بعد المعاينة الطبية.',
                'created_at' => now(),
            ]
        );
    }

    private function person(
        string $email,
        string $fullName,
        int $sequence,
        bool $active,
        string $gender
    ): array {
        $password = config(
            'healthcare_seed.password',
            'password123'
        );

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $fullName,
                'password' => Hash::make($password),
                'is_active' => $active,
            ]
        );

        $profile = Profile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $fullName,
                'national_number' =>
                    sprintf('SEED%016d', $sequence),
                'phone' => $this->phoneFor($sequence),
                'gender' => $gender,
                'address' => $this->addressFor($sequence),
                'date_of_birth' => Carbon::create(
                    1980 + ($sequence % 20),
                    1 + ($sequence % 12),
                    1 + ($sequence % 25)
                )->toDateString(),
            ]
        );

        return [$user, $profile];
    }

    private function arabicName(int $sequence): array
    {
        $maleNames = [
            'أحمد',
            'محمد',
            'سامر',
            'ياسر',
            'لؤي',
            'مازن',
            'حسام',
            'فراس',
            'باسل',
            'خالد',
            'رامي',
            'علاء',
            'طارق',
            'وائل',
            'نزار',
            'شادي',
            'مهند',
            'أيهم',
            'تيم',
            'كنان',
            'أنس',
            'عمار',
            'أسامة',
            'معاذ',
            'سليم',
            'إياد',
            'قيس',
            'رائد',
        ];

        $femaleNames = [
            'سارة',
            'نور',
            'ريم',
            'رنا',
            'ليان',
            'هبة',
            'ديمة',
            'دانا',
            'آية',
            'ميس',
            'مرح',
            'جود',
            'لينا',
            'لمى',
            'يارا',
            'ربى',
            'نادين',
            'سمر',
            'علا',
            'فرح',
            'جنى',
            'روان',
            'بيان',
            'مريم',
            'شهد',
            'تسنيم',
            'ريهام',
            'هناء',
            'سيرين',
        ];

        $familyNames = [
            'الحسن',
            'الأحمد',
            'العلي',
            'الخطيب',
            'الدروبي',
            'الحمصي',
            'الشامي',
            'النجار',
            'الحداد',
            'مراد',
            'عثمان',
            'ديب',
            'صالح',
            'حمود',
            'خليل',
            'درويش',
            'منصور',
            'قاسم',
            'زيدان',
            'إبراهيم',
            'حمدان',
            'العبدالله',
            'الرفاعي',
            'السالم',
            'عيسى',
            'كحيل',
        ];

        $isFemale = $sequence % 2 === 0;

        $names = $isFemale
            ? $femaleNames
            : $maleNames;

        $firstName = $names[
            ($sequence - 1) % count($names)
        ];

        $familyName = $familyNames[
            intdiv($sequence - 1, count($names))
            % count($familyNames)
        ];

        return [
            "{$firstName} {$familyName}",
            $isFemale ? 'female' : 'male',
        ];
    }

    private function doctorQualification(int $index): string
    {
        return $index % 3 === 0
            ? 'بورد اختصاصي'
            : 'دكتور في الطب';
    }

    private function doctorAchievement(int $index): string
    {
        $achievements = [
            'المشاركة في برامج تدريبية للرعاية الطبية المتكاملة.',
            'المشاركة في المؤتمرات الطبية وبرامج التعليم المستمر.',
            'المساهمة في تحسين إجراءات متابعة المرضى.',
            'المشاركة في برامج التوعية الصحية المجتمعية.',
        ];

        return $achievements[
            ($index - 1) % count($achievements)
        ];
    }

    private function bloodType(int $index): string
    {
        $types = [
            'A+',
            'A-',
            'B+',
            'B-',
            'AB+',
            'AB-',
            'O+',
            'O-',
        ];

        return $types[
            ($index - 1) % count($types)
        ];
    }

    private function emergencyContactName(int $index): string
    {
        [$name] = $this->arabicName(500000 + $index);

        return $name;
    }

    private function emergencyContactPhone(int $index): string
    {
        return sprintf(
            '+963 94 %04d %04d',
            intdiv($index, 10000),
            $index % 10000
        );
    }

    private function phoneFor(int $sequence): string
    {
        return sprintf(
            '+963 93 %04d %04d',
            intdiv($sequence, 10000) % 10000,
            $sequence % 10000
        );
    }

    private function addressFor(int $sequence): string
    {
        $addresses = [
            'دمشق، المزة',
            'دمشق، أبو رمانة',
            'دمشق، كفرسوسة',
            'دمشق، الميدان',
            'دمشق، ركن الدين',
            'دمشق، القصاع',
            'دمشق، الزاهرة',
            'دمشق، برزة',
            'دمشق، دمر',
            'ريف دمشق، جرمانا',
        ];

        return $addresses[
            ($sequence - 1) % count($addresses)
        ];
    }

    private function deactivateLastProfessional(
        string $email
    ): void {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (!$user) {
            return;
        }

        $user->update([
            'is_active' => false,
        ]);

        $employee = $user->profile?->employee;

        $employee?->update([
            'is_active' => false,
        ]);
    }
}

