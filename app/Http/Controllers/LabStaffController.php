<?php

namespace App\Http\Controllers;

use App\Models\LabStaff;
use App\Models\Facility;
use App\Http\Requests\StoreLabStaffRequest;
use App\Http\Requests\UpdateLabStaffRequest;
use App\Services\LabStaffService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LabStaffController extends Controller
{
    protected LabStaffService $labStaffService;

    public function __construct(LabStaffService $labStaffService)
    {
        $this->labStaffService = $labStaffService;
    }


    public function index(): JsonResponse
    {
        $this->authorize('viewAny', LabStaff::class);
        $staff = $this->labStaffService->getAllStaff(request()->user());
        return response()->json([
            'success' => true,
            'data'    => $staff
        ], Response::HTTP_OK);
    }


    public function store(StoreLabStaffRequest $request): JsonResponse
    {
        $data = $request->validated();
        $facility = Facility::findOrFail($data['facility_id']);
        $this->authorize('create', [LabStaff::class, $facility]);
        $staff = $this->labStaffService->createStaff($data);
        return response()->json([
            'success' => true,
            'message' => 'Lab staff member created successfully.',
            'data'    => $staff
        ], Response::HTTP_CREATED);
    }


    public function show(int $id): JsonResponse
    {
        $staff = $this->labStaffService->getStaffById($id);
        $this->authorize('view', $staff);
        return response()->json([
            'success' => true,
            'data'    => $staff
        ], Response::HTTP_OK);
    }

    public function update(UpdateLabStaffRequest $request, int $id): JsonResponse
    {
        $staff = $this->labStaffService->getStaffById($id);
        $data = $request->validated();
        $this->authorize('update', $staff);
        if (request()->user()->isManager() && isset($data['facility_id'])) {
            abort_unless(request()->user()->managesFacility(Facility::findOrFail($data['facility_id'])), 403);
        }
        $this->labStaffService->updateStaff($id, $data);
        return response()->json([
            'success' => true,
            'message' => 'Lab staff records updated successfully.'
        ], Response::HTTP_OK);
    }


    public function destroy(int $id): JsonResponse
    {
        $staff = $this->labStaffService->getStaffById($id);
        $this->authorize('delete', $staff);
        $this->labStaffService->deleteStaff($id);
        return response()->json([
            'success' => true,
            'message' => 'Lab staff record deleted successfully.'
        ], Response::HTTP_OK);
    }
}
