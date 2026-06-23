<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabResultRequest;
use App\Http\Requests\UpdateLabResultRequest;
use App\Services\LabResultService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class LabResultController extends Controller
{
    protected LabResultService $labResultService;

    public function __construct(LabResultService $labResultService)
    {
        $this->labResultService = $labResultService;
    }

    public function index(): JsonResponse
    {
        $results = $this->labResultService->getAllLabResults();
        return response()->json([
            'success' => true,
            'data'    => $results
        ], Response::HTTP_OK);
    }

    public function store(StoreLabResultRequest $request): JsonResponse
    {
        $result = $this->labResultService->createLabResult($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab result created successfully.',
            'data'    => $result
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->labResultService->getLabResultById($id);
        return response()->json([
            'success' => true,
            'data'    => $result
        ], Response::HTTP_OK);
    }

    public function update(UpdateLabResultRequest $request, int $id): JsonResponse
    {
        $this->labResultService->updateLabResult($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab result updated successfully.'
        ], Response::HTTP_OK);
    }


    public function destroy(int $id): JsonResponse
    {
        $this->labResultService->deleteLabResult($id);
        return response()->json([
            'success' => true,
            'message' => 'Lab result deleted successfully.'
        ], Response::HTTP_OK);
    }
    
}
