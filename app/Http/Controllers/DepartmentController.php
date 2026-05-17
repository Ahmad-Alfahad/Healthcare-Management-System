<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function index(): JsonResponse
    {
        $departments = $this->departmentService->getAllDepartments();
        return response()->json(['success' => true, 'data' => $departments], Response::HTTP_OK);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = $this->departmentService->createDepartment($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'data'    => $department
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $department = $this->departmentService->getDepartmentById($id);
        return response()->json(['success' => true, 'data' => $department], Response::HTTP_OK);
    }

    public function update(UpdateDepartmentRequest $request, int $id): JsonResponse
    {
        $this->departmentService->updateDepartment($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->departmentService->deleteDepartment($id);
        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.'
        ], Response::HTTP_OK);
    }
}
