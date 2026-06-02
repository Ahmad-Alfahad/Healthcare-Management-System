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
        $dispensings = $this->dispensingService->all();
        return response()->json(['success' => true, 'data' => $dispensings], Response::HTTP_OK);
    }

    public function store(StoreDispensingRequest $request): JsonResponse
    {
        $dispensing = $this->dispensingService->create($request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Dispensing record created successfully.', 
            'data'    => $dispensing
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $dispensing = $this->dispensingService->find($id);
        return response()->json(['success' => true, 'data' => $dispensing], Response::HTTP_OK);
    }

    public function update(UpdateDispensingRequest $request, int $id): JsonResponse
    {
        $this->dispensingService->update($id, $request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Dispensing record updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->dispensingService->delete($id);
        return response()->json([
            'success' => true, 
            'message' => 'Dispensing record deleted successfully.'
        ], Response::HTTP_OK);
    }
}