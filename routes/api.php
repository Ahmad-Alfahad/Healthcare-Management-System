<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LabStaffController;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\MedicalConditionController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionItemController;
use App\Http\Controllers\DispensingController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\LabRequestItemController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\PatientMedicalConditionController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\FacilityDepartmentSpecializationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/user', [UserController::class, 'currentUser'])->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Current user session
    Route::get('/me', [UserController::class, 'currentUser']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Publicly readable reference data for authenticated users
    Route::apiResource('specialization', SpecializationController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('medical-conditions', MedicalConditionController::class);

    // Facilities and medical staff
    Route::apiResource('facilities', FacilityController::class);
    Route::get('facilities/{facility}/departments', [FacilityController::class, 'departments']);
    Route::post('facilities/{facility}/departments', [FacilityController::class, 'addDepartment']);
    Route::delete('facilities/departments/{facilityDepartment}', [FacilityController::class, 'removeDepartment']);
    Route::apiResource('facility-dept-specs', FacilityDepartmentSpecializationController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('labstaff', LabStaffController::class);
    Route::apiResource('pharmacists', PharmacistController::class);
    Route::apiResource('doctor-schedule', DoctorScheduleController::class);

    // Patients and appointments
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('available-slots', [AppointmentController::class, 'availableSlots']);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'changeStatus']);

    // Clinical records
    Route::apiResource('visits', VisitController::class);
    Route::patch('visits/{id}/status', [VisitController::class, 'changeStatus']);
    Route::apiResource('diagnoses', DiagnosisController::class);
    Route::apiResource('prescriptions', PrescriptionController::class);
    Route::patch('prescriptions/{id}/cancel', [PrescriptionController::class, 'cancel']);
    Route::apiResource('prescription-items', PrescriptionItemController::class);
    Route::apiResource('dispensings', DispensingController::class);

    // Laboratory records
    Route::apiResource('lab-tests', LabTestController::class);
    Route::apiResource('lab-request-items', LabRequestItemController::class);
    Route::apiResource('lab-results', LabResultController::class);

    // Patient medical history
    Route::apiResource('patient-medical-conditions', PatientMedicalConditionController::class);
    Route::apiResource('profiles', ProfileController::class);

    // Administrative access control
    Route::middleware('role:admin')->group(function () {
        Route::get('roles-permissions', [RolePermissionController::class, 'index']);
        Route::post('roles-permissions/sync-role/{user}', [RolePermissionController::class, 'syncUserAccess']);
    });
});
