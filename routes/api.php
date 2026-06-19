<?php

use App\Http\Controllers\Api\ProfileController;
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
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('specialization', SpecializationController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('labstaff', LabStaffController::class);
    Route::apiResource('pharmacists', PharmacistController::class);
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('medical_conditions', MedicalConditionController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('available-slots', [AppointmentController::class, 'availableSlots']);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'changeStatus']);
    Route::apiResource('doctor-schedule', DoctorScheduleController::class);
    Route::apiResource('visits', VisitController::class);
    Route::patch('visits/{id}/status',[VisitController::class, 'changeStatus']);
    Route::apiResource('diagnoses', DiagnosisController::class);
    Route::apiResource('prescriptions', PrescriptionController::class);
    Route::apiResource('prescription-items', PrescriptionItemController::class);
    Route::apiResource('dispensings', DispensingController::class);
    Route::apiResource('lab-tests', LabTestController::class);
    Route::apiResource('lab-request-items', LabRequestItemController::class);
    Route::apiResource('lab-results', LabResultController::class);
    Route::apiResource('patient-medical-conditions', PatientMedicalConditionController::class);
    Route::apiResource('profiles', \App\Http\Controllers\ProfileController::class);
    Route::apiResource('roles-permissions', RolePermissionController::class);
    Route::post('/roles-permissions/sync-role/{user}', [RolePermissionController::class, 'syncUserAccess']);
    // User Management
    Route::post('/logout', [UserController::class, 'logout']);
});
