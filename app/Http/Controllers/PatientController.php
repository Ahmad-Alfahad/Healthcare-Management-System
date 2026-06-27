<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PatientController extends Controller
{
    protected PatientService $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function index(): JsonResponse
    {
        $patients = $this->patientService->getAllPatients();
        return response()->json(['data' => $patients], Response::HTTP_OK);
    }

    public function store(StorePatientRequest $request): JsonResponse
    {

        $patient = $this->patientService->createPatient($request->validated());
        return response()->json(['message' => 'Patient created successfully', 'data' => $patient], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $patientData = $this->patientService->getPatient($id);
        return response()->json(['data' => $patientData], Response::HTTP_OK);
    }

    public function update(UpdatePatientRequest $request, int $id): JsonResponse
    {
        $updatedPatient = $this->patientService->updatePatient($id, $request->validated());
        return response()->json(['message' => 'Patient updated successfully', 'data' => $updatedPatient], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {

        $this->patientService->deletePatient($id);
        return response()->json(['message' => 'Patient deleted successfully'], Response::HTTP_OK);
    }
}
