<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreDispensingRequest;
use App\Http\Requests\UpdateDispensingRequest;
use App\Services\DispensingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DispensingController extends Controller
{
    protected $dispensingService;

    public function __construct(DispensingService $dispensingService)
    {
        $this->dispensingService = $dispensingService;
    }

    public function index(): JsonResponse
    {
        $dispensings = $this->dispensingService->getAllDispensings();
        return response()->json(['success' => true, 'data' => $dispensings], Response::HTTP_OK);
    }

    public function store(StoreDispensingRequest $request): JsonResponse
    {
        $dispensing = $this->dispensingService->createDispensing($request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Dispensing record created successfully.', 
            'data'    => $dispensing
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $dispensing = $this->dispensingService->getDispensingById($id);
        return response()->json(['success' => true, 'data' => $dispensing], Response::HTTP_OK);
    }

    public function update(UpdateDispensingRequest $request, int $id): JsonResponse
    {
        $this->dispensingService->updateDispensing($id, $request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Dispensing record updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->dispensingService->deleteDispensing($id);
        return response()->json([
            'success' => true, 
            'message' => 'Dispensing record deleted successfully.'
        ], Response::HTTP_OK);
    }
}