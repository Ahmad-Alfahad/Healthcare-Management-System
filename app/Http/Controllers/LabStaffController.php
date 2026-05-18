<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabStaffRequest;
use App\Http\Requests\UpdateLabStaffRequest;
use App\Services\LabStaffService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LabStaffController extends Controller
{
    protected $labStaffService;

    public function __construct(LabStaffService $labStaffService)
    {
        $this->labStaffService = $labStaffService;
    }

  
    public function index(): JsonResponse
    {
        $staff = $this->labStaffService->getAllStaff();
        return response()->json([
            'success' => true,
            'data'    => $staff
        ], Response::HTTP_OK);
    }

  
    public function store(StoreLabStaffRequest $request): JsonResponse
    {
        $staff = $this->labStaffService->createStaff($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab staff member created successfully.',
            'data'    => $staff
        ], Response::HTTP_CREATED);
    }

   
    public function show(int $id): JsonResponse
    {
        $staff = $this->labStaffService->getStaffById($id);
        return response()->json([
            'success' => true,
            'data'    => $staff
        ], Response::HTTP_OK);
    }

    public function update(UpdateLabStaffRequest $request, int $id): JsonResponse
    {
        $this->labStaffService->updateStaff($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Lab staff records updated successfully.'
        ], Response::HTTP_OK);
    }

   
    public function destroy(int $id): JsonResponse
    {
        $this->labStaffService->deleteStaff($id);
        return response()->json([
            'success' => true,
            'message' => 'Lab staff record deleted successfully.'
        ], Response::HTTP_OK);
    }
}
