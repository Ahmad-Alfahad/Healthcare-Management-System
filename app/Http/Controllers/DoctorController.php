<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
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

        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'page' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'status' => ['sometimes', 'string']]);
        $doctors = $this->doctorService->getAllDoctors($user, $filters);
        return response()->json(['success' => true, 'data' => $doctors], Response::HTTP_OK);
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
