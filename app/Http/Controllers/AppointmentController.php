<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->appointmentService->getAllAppointments()
        ], Response::HTTP_OK);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment =
            $this->appointmentService
                ->createAppointment(
                    $request->validated()
                );

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully.',
            'data' => $appointment
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->appointmentService->getAppointment($id)
        ], Response::HTTP_OK);
    }


    public function update(UpdateAppointmentRequest $request, int $id): JsonResponse
    {

        $this->appointmentService
        ->updateAppointment(
            $id,
            $request->validated()
            );
            
        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {

        $this->appointmentService
            ->deleteAppointment($id);

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully.'
        ], Response::HTTP_OK);
    }

}
