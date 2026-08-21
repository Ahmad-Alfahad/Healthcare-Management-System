<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Http\Requests\StoreDiagnosisRequest;
use App\Http\Requests\UpdateDiagnosisRequest;
use App\Models\Visit;
use App\Services\DiagnosisService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class DiagnosisController extends Controller
{
    protected DiagnosisService $diagnosisService;

    public function __construct(DiagnosisService $diagnosisService)
    {
        $this->diagnosisService = $diagnosisService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Diagnosis::class);
        $diagnoses = $this->diagnosisService->getAllDiagnoses(request()->user());

        return response()->json(['success' => true, 'data' => $diagnoses], Response::HTTP_OK);
    }

    public function store(StoreDiagnosisRequest $request): JsonResponse
    {
        $visit = Visit::findOrFail($request->validated()['visit_id']);
        $this->authorize('create', [Diagnosis::class, $visit]);
        $diagnosis = $this->diagnosisService->createDiagnosis($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis created successfully.',
            'data'    => $diagnosis
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $diagnosis = $this->diagnosisService->getDiagnosisById($id);
        $this->authorize('view', $diagnosis);

        return response()->json(['success' => true, 'data' => $diagnosis], Response::HTTP_OK);
    }

    public function update(UpdateDiagnosisRequest $request, int $id): JsonResponse
    {
        $diagnosis = $this->diagnosisService->getDiagnosisById($id);
        $this->authorize('update', $diagnosis);
        $this->diagnosisService->updateDiagnosis($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $diagnosis = $this->diagnosisService->getDiagnosisById($id);
        $this->authorize('delete', $diagnosis);
        $this->diagnosisService->deleteDiagnosis($id);

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis deleted successfully.'
        ], Response::HTTP_OK);
    }
}
