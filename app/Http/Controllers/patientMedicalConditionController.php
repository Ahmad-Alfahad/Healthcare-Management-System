<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientMedicalConditionRequest;
use App\Http\Requests\UpdatePatientMedicalConditionRequest;
use App\Models\Patient;
use App\Models\PatientMedicalCondition;
use App\Services\PatientMedicalConditionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
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
        $this->authorize('viewAny', PatientMedicalCondition::class);
        $patient_medical_conditions = $this->patientMedicalConditionService
            ->getAll()
            ->filter(fn(PatientMedicalCondition $condition): bool => Gate::allows('view', $condition));

        return response()->json(['success' => true, 'data' => $patient_medical_conditions], Response::HTTP_OK);
    }

    public function store(StorePatientMedicalConditionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $patient = Patient::findOrFail($data['patient_id']);
        $this->authorize('create', [PatientMedicalCondition::class, $patient]);
        $patient_medical_condition = $this->patientMedicalConditionService->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Patient Medical Condition created successfully.',
            'data'    => $patient_medical_condition
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $patient_medical_condition = $this->patientMedicalConditionService->getById($id);
        $this->authorize('view', $patient_medical_condition);
        return response()->json(['success' => true, 'data' => $patient_medical_condition], Response::HTTP_OK);
    }

    public function update(UpdatePatientMedicalConditionRequest $request, int $id): JsonResponse
    {
        $patient_medical_condition = $this->patientMedicalConditionService->getById($id);
        $this->authorize('update', $patient_medical_condition);
        $this->patientMedicalConditionService->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Patient Medical Condition updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $patient_medical_condition = $this->patientMedicalConditionService->getById($id);
        $this->authorize('delete', $patient_medical_condition);
        $this->patientMedicalConditionService->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Patient Medical Condition deleted successfully.'
        ], Response::HTTP_OK);
    }
}
