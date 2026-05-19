<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Services\FacilityService;
use Illuminate\Http\JsonResponse;

class FacilityController extends Controller
{
    protected $facilityService;

    public function __construct(FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function index(): JsonResponse
    {
        $facilities = $this->facilityService->getAllFacilities();
        return response()->json(['success' => true, 'data' => $facilities], 200);
    }

    public function store(StoreFacilityRequest $request): JsonResponse
    {
        $facility = $this->facilityService->createFacility($request->validated());
        return response()->json(['success' => true, 'message' => 'Facility created successfully', 'data' => $facility],201);
    }

    public function show(int $id): JsonResponse
    {
        $facility = $this->facilityService->getFacilityById($id);
        return response()->json(['success' => true, 'data' => $facility], 200);
    }

    public function update(UpdateFacilityRequest $request, int $id): JsonResponse
    {
        $this->facilityService->updateFacility($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'Facility updated successfully'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->facilityService->deleteFacility($id);
        return response()->json(['success' => true, 'message' => 'Facility deleted successfully'], 200);
    }
}