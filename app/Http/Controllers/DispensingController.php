<?php

namespace App\Http\Controllers;

use App\Models\Dispensing;
use App\Models\PrescriptionItem;
use App\Http\Requests\StoreDispensingRequest;
use App\Http\Requests\UpdateDispensingRequest;
use App\Services\DispensingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DispensingController extends Controller
{
    protected DispensingService $dispensingService;

    public function __construct(DispensingService $dispensingService)
    {
        $this->dispensingService = $dispensingService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Dispensing::class);
        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'page' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $dispensings = $this->dispensingService->getAllDispensings($request->user(), $filters);
        return response()->json(['success' => true, 'data' => $dispensings], Response::HTTP_OK);
    }

    public function store(StoreDispensingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $item = PrescriptionItem::with('prescription.visit')->findOrFail($data['prescription_item_id']);
       // $pharmacist = $request->user()->pharmacist;
        $this->authorize('create', [Dispensing::class, $item, $user->pharmacist]);
        $dispensing = $this->dispensingService->createDispensing($data , $user);
        return response()->json([
            'success' => true,
            'message' => 'Dispensing record created successfully.',
            'data'    => $dispensing
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $dispensing = $this->dispensingService->getDispensingById($id);
        $this->authorize('view', $dispensing);
        return response()->json(['success' => true, 'data' => $dispensing], Response::HTTP_OK);
    }

    public function update(UpdateDispensingRequest $request, int $id): JsonResponse
    {
        $dispensing = $this->dispensingService->getDispensingById($id);
        $this->authorize('update', $dispensing);
        $this->dispensingService->updateDispensing($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Dispensing record updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $dispensing = $this->dispensingService->getDispensingById($id);
        $this->authorize('delete', $dispensing);
        $this->dispensingService->deleteDispensing($id);
        return response()->json([
            'success' => true,
            'message' => 'Dispensing record deleted successfully.'
        ], Response::HTTP_OK);
    }
}
