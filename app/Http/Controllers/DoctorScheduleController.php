<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorScheduleRequest;
use App\Http\Requests\UpdateDoctorScheduleRequest;
use App\Services\DoctorScheduleService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DoctorScheduleController extends Controller
{
    protected $doctorScheduleService;
    public function __construct(DoctorScheduleService $doctorScheduleService)
    {
        $this->doctorScheduleService = $doctorScheduleService;
    }

    public function index()
    {
        $doctorSchedules = $this->doctorScheduleService->getAllDoctorSchedules();
        return response()->json(['success' => true, 'data' => $doctorSchedules], Response::HTTP_OK);
    }


    public function store(StoreDoctorScheduleRequest $request): JsonResponse
    {
        $doctorSchedule = $this->doctorScheduleService->createDoctorSchedule($request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Doctor schedule created successfully.', 
            'data'    => $doctorSchedule
        ], Response::HTTP_CREATED);
    }


    public function show(int $id): JsonResponse
    {
        $doctorSchedule = $this->doctorScheduleService->getDoctorScheduleById($id);
        return response()->json(['success' => true, 'data' => $doctorSchedule], Response::HTTP_OK);
    }




    public function update(UpdateDoctorScheduleRequest $request,  int $id):JsonResponse
    {
        $this->doctorScheduleService->updateDoctorSchedule($id, $request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Doctor schedule updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->doctorScheduleService->deleteDoctorSchedule($id);
        return response()->json([
            'success' => true,
            'message' => 'Doctor schedule deleted successfully.'
        ], Response::HTTP_OK);
    }
}
