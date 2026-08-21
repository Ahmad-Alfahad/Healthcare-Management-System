<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Http\Requests\StoreLab_testRequest;
use App\Http\Requests\UpdateLab_testRequest;
use App\Services\LabTestService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LabTestController extends Controller
{

    protected LabTestService $labTestService;
    public function __construct(LabTestService $labTestService)
    {
        $this->labTestService = $labTestService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', LabTest::class);
        $tests = $this->labTestService->getAllTests();
        return response()->json([
            'success' => true,
            'data'    => $tests
        ], Response::HTTP_OK);
    }

    public function store(StoreLab_testRequest $request): JsonResponse
    {
        $this->authorize('create', LabTest::class);
        $test = $this->labTestService->createTest($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab test created successfully.',
            'data'    => $test
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $test = $this->labTestService->getTestById($id);
        $this->authorize('view', $test);
        return response()->json([
            'success' => true,
            'data'    => $test
        ], Response::HTTP_OK);
    }

    public function update(UpdateLab_testRequest $request, int $id): JsonResponse
    {
        $test = $this->labTestService->getTestById($id);
        $this->authorize('update', $test);
        $this->labTestService->updateTest($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab test records updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $test = $this->labTestService->getTestById($id);
        $this->authorize('delete', $test);
        $this->labTestService->deleteTest($id);
        return response()->json([
            'success' => true,
            'message' => 'Lab test deleted successfully.'
        ], Response::HTTP_OK);
    }
}
