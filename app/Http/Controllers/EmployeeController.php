<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Facility;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Employee::class);
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'facility_id' => ['sometimes', 'integer', 'exists:facilities,id'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->employeeService->getAllEmployees(
                $request->user(),
                $filters
            ),
        ], Response::HTTP_OK);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $facility = Facility::findOrFail($data['facility_id']);

        $this->authorize('create', [Employee::class, $facility]);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully.',
            'data' => $this->employeeService->createEmployee($data),
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $this->authorize('view', $employee);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ], Response::HTTP_OK);
    }

    public function update(
        UpdateEmployeeRequest $request,
        int $id
    ): JsonResponse {
        $employee = $this->employeeService->getEmployeeById($id);
        $this->authorize('update', $employee);
        $data = $request->validated();

        if (isset($data['facility_id'])) {
            $facility = Facility::findOrFail($data['facility_id']);
            $this->authorize('assign', [$employee, $facility]);
        }

        $this->employeeService->updateEmployee($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Employee records updated successfully.',
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $this->authorize('delete', $employee);
        $this->employeeService->deleteEmployee($id);

        return response()->json([
            'success' => true,
            'message' => 'Employee record deleted successfully.',
        ], Response::HTTP_OK);
    }
}
