<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabRequestItemRequest;
use App\Http\Requests\UpdateLabRequestItemRequest;
use App\Services\LabRequestItemService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LabRequestItemController extends Controller
{
    protected LabRequestItemService $labRequestItemService;

    public function __construct(LabRequestItemService $labRequestItemService)
    {
        $this->labRequestItemService = $labRequestItemService;
    }

    public function index(): JsonResponse
    {
        $items = $this->labRequestItemService->getAllLabRequestItems();
        return response()->json([
            'success' => true,
            'data'    => $items
        ], Response::HTTP_OK);
    }

    public function store(StoreLabRequestItemRequest $request): JsonResponse
    {
        $item = $this->labRequestItemService->createLabRequestItem($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab request item created successfully.',
            'data'    => $item
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->labRequestItemService->getLabRequestItemById($id);
        return response()->json([
            'success' => true,
            'data'    => $item
        ], Response::HTTP_OK);
    }

    public function update(UpdateLabRequestItemRequest $request, int $id): JsonResponse
    {
        $this->labRequestItemService->updateLabRequestItem($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab request item updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->labRequestItemService->deleteLabRequestItem($id);
        return response()->json([
            'success' => true,
            'message' => 'Lab request item deleted successfully.'
        ], Response::HTTP_OK);
    }

}
