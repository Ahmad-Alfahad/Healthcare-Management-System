<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\SpecializationController;
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
    // User Management
    Route::post('/logout', [UserController::class, 'logout']);
});
