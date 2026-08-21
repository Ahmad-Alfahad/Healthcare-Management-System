<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Visit;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Services\PrescriptionService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionController extends Controller
{
    protected PrescriptionService $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Prescription::class);
        $prescriptions = $this->prescriptionService->getAllPrescriptions();
        return response()->json(['success' => true, 'data' => $prescriptions], Response::HTTP_OK);
    }

    public function store(StorePrescriptionRequest $request): JsonResponse
    {
        $visit = Visit::findOrFail($request->validated()['visit_id']);
        $this->authorize('create', [Prescription::class, $visit]);
        $prescription = $this->prescriptionService->createPrescription($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Prescription created successfully.',
            'data'    => $prescription
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $prescription = $this->prescriptionService->getPrescriptionById($id);
        $this->authorize('view', $prescription);
        return response()->json(['success' => true, 'data' => $prescription], Response::HTTP_OK);
    }

    public function update(UpdatePrescriptionRequest $request, int $id): JsonResponse
    {
        $prescription = $this->prescriptionService->getPrescriptionById($id);
        $this->authorize('update', $prescription);
        $this->prescriptionService->updatePrescription($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Prescription records updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $prescription = $this->prescriptionService->getPrescriptionById($id);
        $this->authorize('delete', $prescription);
        $this->prescriptionService->deletePrescription($id);
        return response()->json([
            'success' => true,
            'message' => 'Prescription deleted successfully.'
        ], Response::HTTP_OK);
    }

    public function cancel(int $id): JsonResponse
    {
        $prescription = $this->prescriptionService->getPrescriptionById($id);
        $this->authorize('cancel', $prescription);
        $this->prescriptionService
            ->cancelPrescription($id);

        return response()->json([
            'success' => true,
            'message' => 'Prescription cancelled successfully.'
        ], Response::HTTP_OK);
    }
}
