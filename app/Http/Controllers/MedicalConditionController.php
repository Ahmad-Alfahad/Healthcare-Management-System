<?php

namespace App\Http\Controllers;

use App\Models\MedicalCondition;
use App\Http\Requests\StoreMedicalConditionRequest;
use App\Http\Requests\UpdateMedicalConditionRequest;
use App\Services\MedicalConditionService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MedicalConditionController extends Controller
{

    protected $medicalConditionService;

    public function __construct(MedicalConditionService $medicalConditionService)
    {
        $this->medicalConditionService = $medicalConditionService;
    }
    public function index(): JsonResponse
    {
        $medical_conditions = $this->medicalConditionService->getAllMedicalCondition();
        return response()->json(['success' => true, 'data' => $medical_conditions], Response::HTTP_OK);
    }

    public function create()
    {
        //
    }

 
    public function store(StoreMedicalConditionRequest $request): JsonResponse
    {
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
        return response()->json(['success' => true, 'data' => $medical_condition], Response::HTTP_OK);
    }


    public function edit(MedicalCondition $medicalCondition)
    {
        //
    }

    public function update(UpdateMedicalConditionRequest $request, int $id): JsonResponse
    {
        $this->medicalConditionService->updateMedicalCondition($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Medical Condition updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->medicalConditionService->deleteMedicalCondition($id);
        return response()->json([
            'success' => true,
            'message' => 'Medical Condition deleted successfully.'
        ], Response::HTTP_OK);
    }
}
