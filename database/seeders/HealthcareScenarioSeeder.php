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
use App\Models\Patient;
use App\Models\PatientMedicalCondition;
use App\Models\Pharmacist;
use App\Models\Profile;
use App\Models\User;
use App\Services\AppointmentService;
use App\Services\DiagnosisService;
use App\Services\DispensingService;
use App\Services\LabRequestItemService;
use App\Services\LabResultService;
use App\Services\PrescriptionItemService;
use App\Services\PrescriptionService;
use App\Services\VisitService;
use Carbon\Carbon;
use Database\Factories\ProfileFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class HealthcareScenarioSeeder extends Seeder
{
    /** @var array<string, mixed> */
    private array $settings;

    public function run(): void
    {
        $this->settings = config('healthcare_seed');

        $this->seedAdministratorsAndManagers();
        $this->seedProfessionals();
        $this->seedPatients();
        $this->seedPatientMedicalConditions();
        $this->seedSchedules();
        $this->seedClinicalScenarios();
    }

    private function seedAdministratorsAndManagers(): void
    {
        [$admin] = $this->person('admin@seed.local', 'System Administrator', 1, true);
        $admin->syncRoles(['admin']);

        $hospital = Facility::query()->where('name', 'Al-Nour Teaching Hospital')->firstOrFail();
        $laboratory = Facility::query()->where('name', 'Al-Nour Diagnostic Laboratory')->firstOrFail();

        foreach ([
            ['hospital-manager@seed.local', 'Hospital Operations Manager', 2, $hospital],
            ['laboratory-manager@seed.local', 'Laboratory Operations Manager', 3, $laboratory],
        ] as [$email, $name, $number, $facility]) {
            [$user, $profile] = $this->person($email, $name, $number, true);
            $user->syncRoles(['manager']);
            Employee::query()->updateOrCreate(['profile_id' => $profile->id], [
                'facility_id' => $facility->id,
                'languages' => ['Arabic', 'English'],
                'is_active' => true,
            ]);
        }
    }

    private function seedProfessionals(): void
    {
        $doctorAssignments = FacilityDepartmentSpecialization::query()
            ->with('facilityDepartment.facility')
            ->where('is_active', true)
            ->whereHas('facilityDepartment.facility', fn ($query) => $query->whereIn('facility_type', ['hospital', 'clinic'])->where('is_active', true))
            ->orderBy('id')
            ->get();

        for ($index = 1; $index <= $this->positiveCount('doctors'); $index++) {
            $assignment = $doctorAssignments[($index - 1) % $doctorAssignments->count()];
            [$user, $profile] = $this->person("doctor-{$index}@seed.local", "Doctor {$index}", 1000 + $index, true);
            $user->syncRoles(['doctor']);
            $employee = Employee::query()->updateOrCreate(['profile_id' => $profile->id], [
                'facility_id' => $assignment->facilityDepartment->facility_id,
                'languages' => $index % 3 === 0 ? ['Arabic', 'English', 'French'] : ['Arabic', 'English'],
                'is_active' => true,
            ]);
            Doctor::query()->updateOrCreate(['employee_id' => $employee->id], [
                'facility_department_specialization_id' => $assignment->id,
                'qualification' => $index % 2 === 0 ? 'MD' : 'Board Certified Specialist',
                'years_of_experience' => 3 + ($index % 20),
                'biography' => "Clinical profile for doctor {$index}.",
                'achievements' => 'Participated in quality improvement programs.',
            ]);
        }

        $pharmacy = Facility::query()->where('name', 'Al-Nour Community Pharmacy')->firstOrFail();
        for ($index = 1; $index <= $this->positiveCount('pharmacists'); $index++) {
            [$user, $profile] = $this->person("pharmacist-{$index}@seed.local", "Pharmacist {$index}", 2000 + $index, true);
            $user->syncRoles(['pharmacist']);
            $employee = Employee::query()->updateOrCreate(['profile_id' => $profile->id], [
                'facility_id' => $pharmacy->id,
                'languages' => ['Arabic', 'English'],
                'is_active' => true,
            ]);
            Pharmacist::query()->updateOrCreate(['employee_id' => $employee->id], [
                'degree' => 'Doctor of Pharmacy', 'years_of_experience' => 2 + ($index % 18),
                'license_number' => sprintf('PHARM-%05d', $index),
            ]);
        }

        $laboratory = Facility::query()->where('name', 'Al-Nour Diagnostic Laboratory')->firstOrFail();
        for ($index = 1; $index <= $this->positiveCount('lab_staff'); $index++) {
            [$user, $profile] = $this->person("lab-staff-{$index}@seed.local", "Laboratory Staff {$index}", 3000 + $index, true);
            $user->syncRoles(['laboratory']);
            $employee = Employee::query()->updateOrCreate(['profile_id' => $profile->id], [
                'facility_id' => $laboratory->id,
                'languages' => ['Arabic', 'English'],
                'is_active' => true,
            ]);
            LabStaff::query()->updateOrCreate(['employee_id' => $employee->id], [
                'specialization' => $index % 2 === 0 ? 'Hematology' : 'Clinical Biochemistry',
                'degree' => 'Bachelor of Medical Laboratory Science',
                'years_of_experience' => 2 + ($index % 18),
                'license_number' => sprintf('LAB-%05d', $index),
            ]);
        }

        // A deactivated account retains its professional history but is not used in transactions.
        if ($this->positiveCount('doctors') > 1) {
            $this->deactivateLastProfessional(Doctor::query()->with('employee.profile.user')->orderByDesc('id')->first());
        }
        if ($this->positiveCount('pharmacists') > 1) {
            $this->deactivateLastProfessional(Pharmacist::query()->with('employee.profile.user')->orderByDesc('id')->first());
        }
        if ($this->positiveCount('lab_staff') > 1) {
            $this->deactivateLastProfessional(LabStaff::query()->with('employee.profile.user')->orderByDesc('id')->first());
        }
    }

    private function seedPatients(): void
    {
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $count = $this->positiveCount('patients');

        for ($index = 1; $index <= $count; $index++) {
            $active = $index !== $count;
            [$user, $profile] = $this->person("patient-{$index}@seed.local", "Patient {$index}", 100000 + $index, $active);
            $user->syncRoles(['patient']);
            Patient::query()->updateOrCreate(['profile_id' => $profile->id], [
                'blood_type' => $bloodTypes[($index - 1) % count($bloodTypes)],
                'emergency_contact_name' => "Emergency Contact {$index}",
                'emergency_contact_phone' => sprintf('+966550%06d', $index),
                'emergency_contact_relation' => ['parent', 'spouse', 'sibling'][$index % 3],
            ]);
        }
    }

    private function seedPatientMedicalConditions(): void
    {
        $conditionIds = \App\Models\MedicalCondition::query()->orderBy('id')->pluck('id');
        $patients = Patient::query()
            ->whereHas('profile.user', fn ($query) => $query->where('email', 'like', 'patient-%@seed.local'))
            ->orderBy('id')
            ->lazyById(500);

        foreach ($patients as $index => $patient) {
            if ($index % 3 !== 0) {
                continue;
            }
            $conditionId = $conditionIds[$index % $conditionIds->count()];
            PatientMedicalCondition::query()->updateOrCreate([
                'patient_id' => $patient->id, 'medical_condition_id' => $conditionId,
            ], [
                'diagnosed_at' => now()->subYears(1 + ($index % 12))->toDateString(),
                'notes' => 'Documented medical history from an earlier consultation.',
            ]);
        }
    }

    private function seedSchedules(): void
    {
        foreach (Doctor::query()->whereHas('employee', fn ($query) => $query->where('is_active', true))->orderBy('id')->cursor() as $doctor) {
            foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                DoctorSchedule::query()->updateOrCreate(['doctor_id' => $doctor->id, 'day_of_week' => $day], [
                    'is_off' => $day === 'Sunday',
                    'start_time' => '08:00:00',
                    'end_time' => '16:00:00',
                    'avg_consultation_time' => 30,
                ]);
            }
        }
    }

    private function seedClinicalScenarios(): void
    {
        $doctors = Doctor::query()->whereHas('employee', fn ($query) => $query->where('is_active', true))->orderBy('id')->get();
        $pharmacistUser = User::query()->whereHas('employee', fn ($query) => $query->where('is_active', true)->whereHas('pharmacist'))->orderBy('id')->firstOrFail();
        $labUser = User::query()->whereHas('employee', fn ($query) => $query->where('is_active', true)->whereHas('labStaff'))->orderBy('id')->firstOrFail();
        $labTests = LabTest::query()->orderBy('id')->get();
        $limit = min($this->positiveCount('transaction_patients'), $this->positiveCount('patients'));

        $patients = Patient::query()
            ->whereHas('profile.user', fn ($query) => $query->where('email', 'like', 'patient-%@seed.local')->where('is_active', true))
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($patients as $index => $patient) {
            $doctor = $doctors[$index % $doctors->count()];
            $slot = intdiv($index, $doctors->count());
            $time = sprintf('%02d:%02d', 8 + intdiv($slot % 16, 2), ($slot % 2) * 30);
            $weekdayOffset = $slot % 6;
            $weekOffset = intdiv($slot, 6);
            $scenario = $index % 4;
            $date = $scenario < 2
                ? now()->startOfWeek(Carbon::MONDAY)->subWeeks(2 + $weekOffset)->addDays($weekdayOffset)
                : now()->startOfWeek(Carbon::MONDAY)->addWeeks(2 + $weekOffset)->addDays($weekdayOffset);

            $key = [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_date' => $date->toDateString(),
                'start_time' => $time,
            ];

            if ($scenario >= 2) {
                $this->seedFutureAppointment($key, $scenario === 3 ? 'cancelled' : 'pending');
                continue;
            }

            $appointment = Appointment::query()->firstOrCreate($key, $key + [
                'status' => 'confirmed',
                'reason' => $scenario === 0 ? 'Historical consultation' : 'Follow-up consultation',
            ]);

            if (! $appointment->wasRecentlyCreated) {
                continue;
            }

            $visit = app(VisitService::class)->startVisit($appointment->id);
            if ($scenario === 0) {
                app(VisitService::class)->completeVisit($visit->id);
                app(AppointmentService::class)->changeStatus($appointment->id, 'completed');
                continue;
            }

            $this->seedActiveVisitRecords($visit->id, $index, $pharmacistUser, $labUser, $labTests);
        }
    }

    /** @param array<string, int|string> $key */
    private function seedFutureAppointment(array $key, string $targetStatus): void
    {
        if (Appointment::query()->where($key)->exists()) {
            return;
        }

        $appointment = app(AppointmentService::class)->createAppointment($key + [
            'status' => 'pending',
            'reason' => $targetStatus === 'cancelled' ? 'Patient requested cancellation' : 'Routine future consultation',
        ]);

        if ($targetStatus === 'cancelled') {
            app(AppointmentService::class)->changeStatus($appointment->id, 'cancelled');
        }
    }

    /** @param \Illuminate\Support\Collection<int, LabTest> $labTests */
    private function seedActiveVisitRecords(int $visitId, int $index, User $pharmacistUser, User $labUser, $labTests): void
    {
        $diagnoses = [
            ['I10', 'Essential hypertension'], ['E11', 'Type 2 diabetes mellitus'],
            ['J45', 'Asthma'], ['L20', 'Atopic dermatitis'],
        ];
        [$code, $description] = $diagnoses[$index % count($diagnoses)];
        app(DiagnosisService::class)->createDiagnosis([
            'visit_id' => $visitId, 'diagnosis_code' => $code, 'description' => $description,
            'diagnosis_type' => 'primary', 'notes' => 'Primary diagnosis recorded during the active visit.',
        ]);
        if ($index % 3 === 0) {
            app(DiagnosisService::class)->createDiagnosis([
                'visit_id' => $visitId, 'diagnosis_code' => 'Z09', 'description' => 'Follow-up examination',
                'diagnosis_type' => 'secondary', 'notes' => 'Supporting follow-up diagnosis.',
            ]);
        }

        $prescription = app(PrescriptionService::class)->createPrescription(['visit_id' => $visitId, 'notes' => 'Medication plan issued during the visit.']);
        $first = app(PrescriptionItemService::class)->createPrescriptionItem([
            'prescription_id' => $prescription->id, 'medication_name' => 'Paracetamol',
            'dosage' => '500 mg', 'quantity_prescribed' => 20, 'frequency' => 'Twice daily', 'duration' => '5 days',
        ]);
        $second = app(PrescriptionItemService::class)->createPrescriptionItem([
            'prescription_id' => $prescription->id, 'medication_name' => 'Omeprazole',
            'dosage' => '20 mg', 'quantity_prescribed' => 14, 'frequency' => 'Once daily', 'duration' => '14 days',
        ]);
        $this->seedDispensingScenario($index % 4, $first->id, $second->id, $pharmacistUser);

        $test = $labTests[$index % $labTests->count()];
        $request = app(LabRequestItemService::class)->createLabRequestItem([
            'visit_id' => $visitId, 'lab_test_id' => $test->id,
            'notes' => 'Laboratory test requested during the active visit.',
        ]);
        $this->seedLabScenario($index % 4, $request->id, $test, $labUser);
    }

    private function seedDispensingScenario(int $scenario, int $firstItemId, int $secondItemId, User $pharmacistUser): void
    {
        if ($scenario === 0) {
            return; // pending prescription
        }
        if ($scenario === 3) {
            app(PrescriptionService::class)->cancelPrescription(\App\Models\PrescriptionItem::findOrFail($firstItemId)->prescription_id);
            return;
        }

        $service = app(DispensingService::class);
        $service->createDispensing(['prescription_item_id' => $firstItemId, 'quantity_dispensed' => $scenario === 1 ? 10 : 20], $pharmacistUser);
        if ($scenario === 2) {
            $service->createDispensing(['prescription_item_id' => $secondItemId, 'quantity_dispensed' => 14], $pharmacistUser);
        }
    }

    private function seedLabScenario(int $scenario, int $requestId, LabTest $test, User $labUser): void
    {
        if ($scenario === 0) {
            return; // pending request
        }
        if ($scenario === 3) {
            LabRequestItem::query()->whereKey($requestId)->update(['status' => 'cancelled']);
            return;
        }

        $request = app(LabRequestItemService::class)->startLabRequest($requestId);
        if ($scenario === 2) {
            app(LabResultService::class)->createLabResult([
                'lab_request_item_id' => $request->id,
                'value' => $test->range_low + (($test->range_high - $test->range_low) / 2),
                'notes' => 'Result reviewed and released by laboratory staff.',
            ], $labUser);
        }
    }

    /** @return array{0: User, 1: Profile} */
    private function person(string $email, string $fullName, int $sequence, bool $active): array
    {
        /** @var UserFactory $userFactory */
        $userFactory = User::factory();
        $user = $userFactory->make([
            'name' => $fullName, 'email' => $email, 'is_active' => $active,
        ]);
        $user = User::query()->updateOrCreate(['email' => $email], $user->only(['name', 'password', 'is_active']));

        /** @var ProfileFactory $profileFactory */
        $profileFactory = Profile::factory();
        $profile = $profileFactory->make([
            'user_id' => $user->id,
            'full_name' => $fullName,
            'national_number' => sprintf('SEED%016d', $sequence),
            'phone' => sprintf('+966500%06d', $sequence),
            'gender' => $sequence % 2 === 0 ? 'female' : 'male',
            'address' => 'Riyadh, Saudi Arabia',
            'date_of_birth' => now()->subYears(22 + ($sequence % 45))->toDateString(),
        ]);
        $profile = Profile::query()->updateOrCreate(['user_id' => $user->id], $profile->only([
            'full_name', 'national_number', 'phone', 'gender', 'address', 'date_of_birth',
        ]));

        return [$user, $profile];
    }

    private function deactivateLastProfessional(?object $professional): void
    {
        if (! $professional || ! $professional->employee) {
            return;
        }

        $professional->employee->update(['is_active' => false]);
        $professional->employee->profile?->user?->update(['is_active' => false]);
    }

    private function positiveCount(string $key): int
    {
        return max(1, (int) ($this->settings[$key] ?? 1));
    }
}
