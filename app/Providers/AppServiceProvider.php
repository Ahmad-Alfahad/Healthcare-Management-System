<?php

namespace App\Providers;

use App\Models\Facility;
use Illuminate\Support\ServiceProvider;
use App\Policies\UserPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\PatientPolicy;
use App\Policies\FacilityDepartmentSpecializationPolicy;
use App\Models\User;
use App\Models\Profile;
use App\Models\Patient;
use App\Policies\FacilityPolicy;
use App\Policies\AppointmentPolicy;
use App\Models\Appointment;
use App\Policies\VisitPolicy;
use App\Models\Visit;
use App\Policies\DiagnosisPolicy;
use App\Models\Diagnosis;
use App\Policies\PrescriptionPolicy;
use App\Models\Prescription;
use App\Policies\PrescriptionItemPolicy;
use App\Models\PrescriptionItem;
use App\Policies\DispensingPolicy;
use App\Models\Dispensing;
use App\Policies\LabTestPolicy;
use App\Models\LabTest;
use App\Policies\LabRequestItemPolicy;
use App\Models\LabRequestItem;
use App\Policies\LabResultPolicy;
use App\Models\LabResult;
use App\Policies\MedicalConditionPolicy;
use App\Models\MedicalCondition;
use App\Policies\PatientMedicalConditionPolicy;
use App\Models\PatientMedicalCondition;
use App\Policies\DepartmentPolicy;
use App\Models\Department;
use App\Policies\SpecializationPolicy;
use App\Models\Specialization;
use App\Models\FacilityDepartmentSpecialization;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Profile::class, ProfilePolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Facility::class, FacilityPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Visit::class, VisitPolicy::class);
        Gate::policy(Diagnosis::class, DiagnosisPolicy::class);
        Gate::policy(Prescription::class, PrescriptionPolicy::class);
        Gate::policy(PrescriptionItem::class, PrescriptionItemPolicy::class);
        Gate::policy(Dispensing::class, DispensingPolicy::class);
        Gate::policy(LabTest::class, LabTestPolicy::class);
        Gate::policy(LabRequestItem::class, LabRequestItemPolicy::class);
        Gate::policy(LabResult::class, LabResultPolicy::class);
        Gate::policy(MedicalCondition::class, MedicalConditionPolicy::class);
        Gate::policy(PatientMedicalCondition::class, PatientMedicalConditionPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Specialization::class, SpecializationPolicy::class);
        Gate::policy(
            FacilityDepartmentSpecialization::class,
            FacilityDepartmentSpecializationPolicy::class
        );
    }
}
