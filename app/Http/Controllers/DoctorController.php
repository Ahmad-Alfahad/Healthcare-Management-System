<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Department;
use App\Models\Facility;
use App\Models\FacilityDepartment;
use App\Models\FacilityDepartmentSpecialization;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Services\DoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorController extends Controller
{
    protected DoctorService $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = request()->user();
        $this->authorize('viewAny', Doctor::class);

        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'facility_id' => ['sometimes', 'integer', 'exists:facilities,id'],
            'department_id' => ['sometimes', 'integer', 'exists:departments,id'],
            'specialization_id' => ['sometimes', 'integer', 'exists:specializations,id'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', 'string'],
        ]);
        $doctors = $this->doctorService->getAllDoctors($user, $filters);
        return response()->json(['success' => true, 'data' => $doctors], Response::HTTP_OK);
    }

    public function facilityDoctors(Request $request, int $facilityId): JsonResponse
    {
        Facility::findOrFail($facilityId);

        return $this->scopedDoctorsResponse($request, [
            'facility_id' => $facilityId,
        ]);
    }

    public function departmentDoctors(
        Request $request,
        int $facilityId,
        int $departmentId
    ): JsonResponse {
        Facility::findOrFail($facilityId);
        Department::findOrFail($departmentId);

        FacilityDepartment::where('facility_id', $facilityId)
            ->where('department_id', $departmentId)
            ->firstOrFail();

        return $this->scopedDoctorsResponse($request, [
            'facility_id' => $facilityId,
            'department_id' => $departmentId,
        ]);
    }

    public function specializationDoctors(
        Request $request,
        int $facilityId,
        int $departmentId,
        int $specializationId
    ): JsonResponse {
        Facility::findOrFail($facilityId);
        Department::findOrFail($departmentId);

        $facilityDepartment = FacilityDepartment::where('facility_id', $facilityId)
            ->where('department_id', $departmentId)
            ->firstOrFail();

        FacilityDepartmentSpecialization::where(
            'facility_department_id',
            $facilityDepartment->id
        )
            ->where('specialization_id', $specializationId)
            ->firstOrFail();

        return $this->scopedDoctorsResponse($request, [
            'facility_id' => $facilityId,
            'department_id' => $departmentId,
            'specialization_id' => $specializationId,
        ]);
    }

    private function scopedDoctorsResponse(
        Request $request,
        array $scope
    ): JsonResponse {
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', 'string'],
        ]);

        $doctors = $this->doctorService->getAllDoctors(
            $request->user(),
            array_merge($filters, $scope)
        );

        return response()->json([
            'success' => true,
            'data' => $doctors,
        ], Response::HTTP_OK);
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $data = $request->validated();

        $facilityDepartmentSpecialization =
            FacilityDepartmentSpecialization::with(
                'facilityDepartment.facility'
            )->findOrFail(
                $data['facility_department_specialization_id']
            );

        $this->authorize(
            'create',
            [
                Doctor::class,
                $facilityDepartmentSpecialization
            ]
        );

        $doctor = $this->doctorService->createDoctor($data);

        return response()->json([
            'success' => true,
            'message' => 'Doctor assigned and created successfully.',
            'data' => $doctor
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $doctor = $this->doctorService->getDoctorById($id);
        $this->authorize('view', $doctor);
        return response()->json(['success' => true, 'data' => $doctor], Response::HTTP_OK);
    }

    public function update(
        UpdateDoctorRequest $request,
        int $id
    ): JsonResponse {
        $doctor = $this->doctorService->getDoctorById($id);

        $this->authorize('update', $doctor);

        $data = $request->validated();

        if (isset($data['facility_department_specialization_id'])) {

            $newFacilityDepartmentSpecialization =
                FacilityDepartmentSpecialization::with(
                    'facilityDepartment.facility'
                )->findOrFail(
                    $data['facility_department_specialization_id']
                );

            $this->authorize(
                'create',
                [
                    Doctor::class,
                    $newFacilityDepartmentSpecialization
                ]
            );
        }

        $this->doctorService->updateDoctor($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Doctor records updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $doctor = $this->doctorService->getDoctorById($id);
        $this->authorize('delete', $doctor);
        $this->doctorService->deleteDoctor($id);
        return response()->json([
            'success' => true,
            'message' => 'Doctor record deleted successfully.'
        ], Response::HTTP_OK);
    }
}
