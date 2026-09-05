<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\Doctor;
use App\Http\Requests\StoreDoctorScheduleRequest;
use App\Http\Requests\UpdateDoctorScheduleRequest;
use App\Services\DoctorScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorScheduleController extends Controller
{
    protected DoctorScheduleService $doctorScheduleService;
    public function __construct(DoctorScheduleService $doctorScheduleService)
    {
        $this->doctorScheduleService = $doctorScheduleService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', DoctorSchedule::class);
        $user = request()->user();
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'day_of_week' => ['sometimes', 'string'],
            'doctor_id' => ['sometimes', 'integer', 'exists:doctors,id'],
        ]);
        $doctorSchedules = $this->doctorScheduleService->getAllDoctorSchedules($user, $filters);
        return response()->json(['success' => true, 'data' => $doctorSchedules], Response::HTTP_OK);
    }


    public function store(StoreDoctorScheduleRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validated();
        $doctor = Doctor::with(
            'facilityDepartmentSpecialization.facilityDepartment.facility'
        )->findOrFail($data['doctor_id']);

        $this->authorize('create', [
            DoctorSchedule::class,
            $doctor
        ]);

        $doctorSchedule = $this->doctorScheduleService
            ->createDoctorSchedule($data, $user);

        return response()->json([
            'success' => true,
            'message' => 'Doctor schedule created successfully.',
            'data'    => $doctorSchedule
        ], Response::HTTP_CREATED);
    }


    public function show(int $id): JsonResponse
    {
        $doctorSchedule = $this->doctorScheduleService->getDoctorScheduleById($id);
        $this->authorize('view', $doctorSchedule);
        return response()->json(['success' => true, 'data' => $doctorSchedule], Response::HTTP_OK);
    }

    public function update(UpdateDoctorScheduleRequest $request,  int $id): JsonResponse
    {
        $doctorSchedule = $this->doctorScheduleService
            ->getDoctorScheduleById($id);

        $this->authorize('update', $doctorSchedule);

        $data = $request->validated();
        if (
            isset($data['doctor_id'])
            && $data['doctor_id'] != $doctorSchedule->doctor_id
        ) {
            $newDoctor = Doctor::with(
                'facilityDepartmentSpecialization.facilityDepartment.facility'
            )->findOrFail($data['doctor_id']);

            $this->authorize('create', [
                DoctorSchedule::class,
                $newDoctor
            ]);
        }

        $this->doctorScheduleService
            ->updateDoctorSchedule($id, $data);
        return response()->json([
            'success' => true,
            'message' => 'Doctor schedule updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $doctorSchedule = $this->doctorScheduleService
            ->getDoctorScheduleById($id);

        $this->authorize('delete', $doctorSchedule);

        $this->doctorScheduleService->deleteDoctorSchedule($id);
        return response()->json([
            'success' => true,
            'message' => 'Doctor schedule deleted successfully.'
        ], Response::HTTP_OK);
    }
}
