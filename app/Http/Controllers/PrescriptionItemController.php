<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionItem;
use App\Http\Requests\StorePrescriptionItemRequest;
use App\Http\Requests\UpdatePrescriptionItemRequest;
use App\Services\PrescriptionItemService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionItemController extends Controller
{
    protected $prescriptionItemService;

    public function __construct(PrescriptionItemService $prescriptionItemService)
    {
        $this->prescriptionItemService = $prescriptionItemService;
    }

    public function index(): JsonResponse
    {
        $items = $this->prescriptionItemService->getAllPrescriptionItems();
        return response()->json(['success' => true, 'data' => $items], Response::HTTP_OK);
    }

    public function store(StorePrescriptionItemRequest $request): JsonResponse
    {
        $item = $this->prescriptionItemService->createPrescriptionItem($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Prescription item created successfully.',
            'data'    => $item
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $item = $this->prescriptionItemService->getPrescriptionItemById($id);
        return response()->json(['success' => true, 'data' => $item], Response::HTTP_OK);
    }

    public function update(UpdatePrescriptionItemRequest $request, int $id): JsonResponse
    {
        $this->prescriptionItemService->updatePrescriptionItem($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Prescription item updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->prescriptionItemService->deletePrescriptionItem($id);
        return response()->json([
            'success' => true,
            'message' => 'Prescription item deleted successfully.'
        ], Response::HTTP_OK);
    }
}
