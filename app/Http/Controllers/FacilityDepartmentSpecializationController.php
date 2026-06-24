<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityDepartmentSpecializationRequest;
use App\Http\Requests\UpdateFacilityDepartmentSpecializationRequest;
use App\Services\FacilityDepartmentSpecializationService;
use Illuminate\Http\JsonResponse;

class FacilityDepartmentSpecializationController extends Controller
{
    protected  FacilityDepartmentSpecializationService $service;

    public function __construct(FacilityDepartmentSpecializationService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $facilities_dept_spec = $this->service->getAll();
        return response()->json(['success' => true, 'data' => $facilities_dept_spec], 200);
    }

    public function store(StoreFacilityDepartmentSpecializationRequest $request): JsonResponse
    {
        $facilities_dept_spec = $this->service->create($request->validated());
        return response()->json(['success' => true, 'message' => 'facilities_dept_spec created successfully', 'data' => $facilities_dept_spec],201);
    }

    public function show(int $id): JsonResponse
    {
        $facilities_dept_spec = $this->service->getById($id);
        return response()->json(['success' => true, 'data' => $facilities_dept_spec], 200);
    }

        public function update(UpdateFacilityDepartmentSpecializationRequest $request, int $id): JsonResponse
    {
        $this->service->update($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'facilities_dept_spec updated successfully'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(['success' => true, 'message' => 'facilities_dept_spec deleted successfully'], 200);
    }
}