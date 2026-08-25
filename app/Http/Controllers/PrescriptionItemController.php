<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionItem;
use App\Models\Prescription;
use App\Http\Requests\StorePrescriptionItemRequest;
use App\Http\Requests\UpdatePrescriptionItemRequest;
use App\Services\PrescriptionItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionItemController extends Controller
{
    protected PrescriptionItemService $prescriptionItemService;

    public function __construct(PrescriptionItemService $prescriptionItemService)
    {
        $this->prescriptionItemService = $prescriptionItemService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PrescriptionItem::class);
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'prescription_id' => ['required', 'integer', 'exists:prescriptions,id'],
        ]);

        $prescription = Prescription::findOrFail($filters['prescription_id']);
        $this->authorize('view', $prescription);

        $items = $this->prescriptionItemService->getAllPrescriptionItems($request->user(), $filters);
        return response()->json(['success' => true, 'data' => $items], Response::HTTP_OK);
    }

    public function store(StorePrescriptionItemRequest $request): JsonResponse
    {
        $prescription = Prescription::findOrFail($request->validated()['prescription_id']);
        $this->authorize('create', [PrescriptionItem::class, $prescription]);
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
        $this->authorize('view', $item);

        return response()->json(['success' => true, 'data' => $item], Response::HTTP_OK);
    }

    public function byPrescription(Prescription $prescription): JsonResponse
    {
        $this->authorize('view', $prescription);

        return response()->json([
            'success' => true,
            'data' => $this->prescriptionItemService->getItemsByPrescriptionId($prescription->id),
        ], Response::HTTP_OK);
    }

    public function update(UpdatePrescriptionItemRequest $request, int $id): JsonResponse
    {
        $item = $this->prescriptionItemService->getPrescriptionItemById($id);
        $this->authorize('update', $item);
        $this->prescriptionItemService->updatePrescriptionItem($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Prescription item updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = $this->prescriptionItemService->getPrescriptionItemById($id);
        $this->authorize('delete', $item);
        $this->prescriptionItemService->deletePrescriptionItem($id);
        return response()->json([
            'success' => true,
            'message' => 'Prescription item deleted successfully.'
        ], Response::HTTP_OK);
    }
}
