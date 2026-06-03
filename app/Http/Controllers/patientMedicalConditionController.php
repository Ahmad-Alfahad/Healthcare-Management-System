<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientMedicalConditionRequest;
use App\Http\Requests\UpdatePatientMedicalConditionRequest;
use App\Services\PatientMedicalConditionService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PatientMedicalConditionController extends Controller
{
    protected $patientMedicalConditionService;

    public function __construct(PatientMedicalConditionService $patientMedicalConditionService)
    {
        $this->patientMedicalConditionService = $patientMedicalConditionService;
    }

    public function index(): JsonResponse
    {
        $patient_medical_conditions = $this->patientMedicalConditionService->getAll();
        return response()->json(['success' => true, 'data' => $patient_medical_conditions], Response::HTTP_OK);
    }

    public function store(StorePatientMedicalConditionRequest $request): JsonResponse
    {
        $patient_medical_condition = $this->patientMedicalConditionService->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Patient Medical Condition created successfully.',
            'data'    => $patient_medical_condition
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $patient_medical_condition = $this->patientMedicalConditionService->getById($id);
        return response()->json(['success' => true, 'data' => $patient_medical_condition], Response::HTTP_OK);
    }

    public function update(UpdatePatientMedicalConditionRequest $request, int $id): JsonResponse
    {
        $this->patientMedicalConditionService->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Patient Medical Condition updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->patientMedicalConditionService->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Patient Medical Condition deleted successfully.'
        ], Response::HTTP_OK);
    }
}