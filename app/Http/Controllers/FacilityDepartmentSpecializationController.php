<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityDepartmentSpecializationRequest;
use App\Http\Requests\UpdateFacilityDepartmentSpecializationRequest;
use App\Models\FacilityDepartment;
use App\Models\FacilityDepartmentSpecialization;
use App\Services\FacilityDepartmentSpecializationService;
use Illuminate\Http\JsonResponse;

class FacilityDepartmentSpecializationController extends Controller
{
    protected FacilityDepartmentSpecializationService $service;

    public function __construct(
        FacilityDepartmentSpecializationService $service
    ) {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $this->authorize(
            'viewAny',
            FacilityDepartmentSpecialization::class
        );

        $facilitiesDeptSpec = $this->service->getAll(request()->user());

        return response()->json([
            'success' => true,
            'data' => $facilitiesDeptSpec
        ], 200);
    }

    public function store(
        StoreFacilityDepartmentSpecializationRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $facilityDepartment = FacilityDepartment::findOrFail(
            $data['facility_department_id']
        );

        $this->authorize(
            'create',
            [
                FacilityDepartmentSpecialization::class,
                $facilityDepartment
            ]
        );

        $assignment = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Specialization assigned successfully',
            'data' => $assignment
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $assignment = $this->service->getById($id);

        $this->authorize(
            'view',
            $assignment
        );

        return response()->json([
            'success' => true,
            'data' => $assignment
        ], 200);
    }

    public function update(
        UpdateFacilityDepartmentSpecializationRequest $request,
        int $id
    ): JsonResponse {
        $assignment = $this->service->getById($id);

        $this->authorize(
            'update',
            $assignment
        );

        $data = $request->validated();

        if (isset($data['facility_department_id'])) {

            $newFacilityDepartment = FacilityDepartment::findOrFail(
                $data['facility_department_id']
            );

            $this->authorize(
                'create',
                [
                    FacilityDepartmentSpecialization::class,
                    $newFacilityDepartment
                ]
            );
        }

        $this->service->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Specialization assignment updated successfully'
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $assignment = $this->service->getById($id);

        $this->authorize(
            'delete',
            $assignment
        );

        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Specialization assignment deleted successfully'
        ], 200);
    }
}
