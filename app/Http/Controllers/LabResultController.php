<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabResultRequest;
use App\Http\Requests\UpdateLabResultRequest;
use App\Models\LabRequestItem;
use App\Models\LabResult;
use App\Models\LabStaff;
use App\Services\LabResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
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
        $this->authorize('viewAny', LabResult::class);
        $results = $this->labResultService
            ->getAllLabResults()
            ->filter(fn(LabResult $result): bool => Gate::allows('view', $result));

        return response()->json([
            'success' => true,
            'data'    => $results
        ], Response::HTTP_OK);
    }

    public function store(StoreLabResultRequest $request): JsonResponse
    {
        $data = $request->validated();
        $labRequestItem = LabRequestItem::findOrFail($data['lab_request_item_id']);
        $labStaff = LabStaff::findOrFail($data['lab_staff_id']);
        $this->authorize('create', [LabResult::class, $labRequestItem, $labStaff]);

        $result = $this->labResultService->createLabResult($data);
        return response()->json([
            'success' => true,
            'message' => 'Lab result created successfully.',
            'data'    => $result
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->labResultService->getLabResultById($id);
        $this->authorize('view', $result);
        return response()->json([
            'success' => true,
            'data'    => $result
        ], Response::HTTP_OK);
    }

    public function update(UpdateLabResultRequest $request, int $id): JsonResponse
    {
        $result = $this->labResultService->getLabResultById($id);
        $this->authorize('update', $result);
        $this->labResultService->updateLabResult($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab result updated successfully.'
        ], Response::HTTP_OK);
    }


    public function destroy(int $id): JsonResponse
    {
        $result = $this->labResultService->getLabResultById($id);
        $this->authorize('delete', $result);
        $this->labResultService->deleteLabResult($id);
        return response()->json([
            'success' => true,
            'message' => 'Lab result deleted successfully.'
        ], Response::HTTP_OK);
    }
}
