<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreMedicalConditionRequest;
use App\Http\Requests\UpdateMedicalConditionRequest;
use App\Models\MedicalCondition;
use App\Services\MedicalConditionService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MedicalConditionController extends Controller
{

    protected MedicalConditionService $medicalConditionService;

    public function __construct(MedicalConditionService $medicalConditionService)
    {
        $this->medicalConditionService = $medicalConditionService;
    }
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MedicalCondition::class);
        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'page' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $medical_conditions = $this->medicalConditionService->getAllMedicalCondition($filters);
        return response()->json(['success' => true, 'data' => $medical_conditions], Response::HTTP_OK);
    }




    public function store(StoreMedicalConditionRequest $request): JsonResponse
    {
        $this->authorize('create', MedicalCondition::class);
        $medical_condition = $this->medicalConditionService->createMedicalCondition($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Medical Condition created successfully.',
            'data'    => $medical_condition
        ], Response::HTTP_CREATED);
    }


    public function show(int $id): JsonResponse
    {
        $medical_condition = $this->medicalConditionService->getMedicalConditionById($id);
        $this->authorize('view', $medical_condition);
        return response()->json(['success' => true, 'data' => $medical_condition], Response::HTTP_OK);
    }




    public function update(UpdateMedicalConditionRequest $request, int $id): JsonResponse
    {
        $medical_condition = $this->medicalConditionService->getMedicalConditionById($id);
        $this->authorize('update', $medical_condition);
        $this->medicalConditionService->updateMedicalCondition($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Medical Condition updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $medical_condition = $this->medicalConditionService->getMedicalConditionById($id);
        $this->authorize('delete', $medical_condition);
        $this->medicalConditionService->deleteMedicalCondition($id);
        return response()->json([
            'success' => true,
            'message' => 'Medical Condition deleted successfully.'
        ], Response::HTTP_OK);
    }
}
