<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabRequestItemRequest;
use App\Http\Requests\UpdateLabRequestItemRequest;
use App\Models\LabRequestItem;
use App\Models\Visit;
use App\Services\LabRequestItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LabRequestItemController extends Controller
{
    protected LabRequestItemService $labRequestItemService;

    public function __construct(LabRequestItemService $labRequestItemService)
    {
        $this->labRequestItemService = $labRequestItemService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', LabRequestItem::class);
        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'page' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'status' => ['sometimes', 'string', 'in:pending,processing,completed,cancelled']]);
        $items = $this->labRequestItemService
            ->getAllLabRequestItems($filters, $request->user())
            ->filter(fn(LabRequestItem $item): bool => $this->authorizeForItem($item));

        return response()->json([
            'success' => true,
            'data'    => $items
        ], Response::HTTP_OK);
    }

    public function store(StoreLabRequestItemRequest $request): JsonResponse
    {
        $visit = Visit::findOrFail($request->validated()['visit_id']);
        $this->authorize('create', [LabRequestItem::class, $visit]);
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
        $this->authorize('view', $item);
        return response()->json([
            'success' => true,
            'data'    => $item
        ], Response::HTTP_OK);
    }

    public function update(UpdateLabRequestItemRequest $request, int $id): JsonResponse
    {
        $item = $this->labRequestItemService->getLabRequestItemById($id);
        $this->authorize('update', $item);
        $this->labRequestItemService->updateLabRequestItem($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab request item updated successfully.'
        ], Response::HTTP_OK);
    }

    public function start(int $id): JsonResponse
    {
        $item = $this->labRequestItemService->getLabRequestItemById($id);
        $this->authorize('update', $item);
        $item = $this->labRequestItemService->startLabRequest($id);

        return response()->json([
            'success' => true,
            'message' => 'Lab request item started successfully.',
            'data' => $item
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = $this->labRequestItemService->getLabRequestItemById($id);
        $this->authorize('delete', $item);
        $this->labRequestItemService->deleteLabRequestItem($id);
        return response()->json([
            'success' => true,
            'message' => 'Lab request item deleted successfully.'
        ], Response::HTTP_OK);
    }

    private function authorizeForItem(LabRequestItem $item): bool
    {
        return Gate::allows('view', $item);
    }
}
