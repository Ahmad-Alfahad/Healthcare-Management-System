<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityDepartmentRequest;
use App\Http\Requests\UpdateFacilityDepartmentRequest;
use App\Services\FacilityDepartmentService;
use Illuminate\Http\JsonResponse;

class FacilityDepartmentController extends Controller
{
    protected FacilityDepartmentService $service;

    public function __construct(FacilityDepartmentService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $facilityDepartments = $this->service->getAll();
        return response()->json(['success' => true, 'data' => $facilityDepartments], 200);
    }

    public function store(StoreFacilityDepartmentRequest $request): JsonResponse
    {
        $facilityDepartment = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Facility department created successfully',
            'data' => $facilityDepartment
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $facilityDepartment = $this->service->getById($id);
        return response()->json(['success' => true, 'data' => $facilityDepartment], 200);
    }

    public function update(UpdateFacilityDepartmentRequest $request, int $id): JsonResponse
    {
        $this->service->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Facility department updated successfully'
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Facility department deleted successfully'
        ], 200);
    }
}
