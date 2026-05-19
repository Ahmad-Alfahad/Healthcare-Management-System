<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Services\DoctorService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DoctorController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index(): JsonResponse
    {
        $doctors = $this->doctorService->getAllDoctors();
        return response()->json(['success' => true, 'data' => $doctors], Response::HTTP_OK);
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $doctor = $this->doctorService->createDoctor($request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Doctor assigned and created successfully.', 
            'data'    => $doctor
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $doctor = $this->doctorService->getDoctorById($id);
        return response()->json(['success' => true, 'data' => $doctor], Response::HTTP_OK);
    }

    public function update(UpdateDoctorRequest $request, int $id): JsonResponse
    {
        $this->doctorService->updateDoctor($id, $request->validated());
        return response()->json([
            'success' => true, 
            'message' => 'Doctor records updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->doctorService->deleteDoctor($id);
        return response()->json([
            'success' => true, 
            'message' => 'Doctor record deleted successfully.'
        ], Response::HTTP_OK);
    }
}
