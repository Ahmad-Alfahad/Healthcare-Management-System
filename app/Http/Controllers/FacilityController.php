<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Models\Facility;
use App\Services\FacilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    protected FacilityService $facilityService;

    public function __construct(FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Facility::class);
        $facilities = $this->facilityService->getAllFacilities();
        return response()->json(['success' => true, 'data' => $facilities], 200);
    }

    public function store(StoreFacilityRequest $request): JsonResponse
    {
        $this->authorize('create', Facility::class);
        $facility = $this->facilityService->createFacility($request->validated());
        return response()->json(['success' => true, 'message' => 'Facility created successfully', 'data' => $facility], 201);
    }

    public function show(int $id): JsonResponse
    {
        $facility = $this->facilityService->getFacilityById($id);
        $this->authorize('view', $facility);
        return response()->json(['success' => true, 'data' => $facility], 200);
    }

    public function update(UpdateFacilityRequest $request, int $id): JsonResponse
    {
        $facility = $this->facilityService->getFacilityById($id);
        $this->authorize('update', $facility);
        $this->facilityService->updateFacility($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'Facility updated successfully'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $facility = $this->facilityService->getFacilityById($id);
        $this->authorize('delete', $facility);
        $this->facilityService->deleteFacility($id);
        return response()->json(['success' => true, 'message' => 'Facility deleted successfully'], 200);
    }

    public function departments(int $facilityId): JsonResponse
    {
        $facility = $this->facilityService->getFacilityById($facilityId);

        $this->authorize('view', $facility);

        $departments = $this->facilityService->getFacilityDepartments($facilityId);

        return response()->json([
            'success' => true,
            'data' => $departments
        ], 200);
    }
    public function addDepartment(Request $request, int $facilityId): JsonResponse
    {
        $facility = $this->facilityService->getFacilityById($facilityId);

        $this->authorize('attachDepartment', $facility);
        $validated = $request->validate([
            'department_id' => [
                'required',
                'exists:departments,id'
            ],
        ]);

        $facilityDepartment = $this->facilityService->addDepartment(
            $facilityId,
            $validated['department_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Department assigned to facility successfully',
            'data' => $facilityDepartment
        ], 201);
    }

    public function removeDepartment(int $facilityDepartmentId): JsonResponse
    {

        $facilityDepartment = $this->facilityService
            ->getFacilityDepartmentById($facilityDepartmentId);

        $facility = $facilityDepartment->facility;

        $this->authorize('detachDepartment', $facility);

        $this->facilityService->removeDepartment($facilityDepartmentId);

        return response()->json([
            'success' => true,
            'message' => 'Department removed from facility successfully'
        ], 200);
    }
}
