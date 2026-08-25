<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\AvailableSlotsRequest;
use App\Http\Requests\ChangeAppointmentStatusRequest;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;


class AppointmentController extends Controller
{
    protected AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Appointment::class);

        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'page' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'status' => ['sometimes', 'string'], 'from' => ['sometimes', 'date'], 'to' => ['sometimes', 'date', 'after_or_equal:from']]);
        $appointments = $this->appointmentService->getAllAppointments(request()->user(), $filters);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ], Response::HTTP_OK);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $patient = Patient::findOrFail($data['patient_id']);
        $doctor = Doctor::findOrFail($data['doctor_id']);

        $this->authorize('create', [Appointment::class, $patient, $doctor]);

        $appointment =
            $this->appointmentService
            ->createAppointment(
                $data
            );

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully.',
            'data' => $appointment
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $appointment = $this->appointmentService->getAppointment($id);
        $this->authorize('view', $appointment);

        return response()->json([
            'success' => true,
            'data' => $appointment
        ], Response::HTTP_OK);
    }


    public function update(UpdateAppointmentRequest $request, int $id): JsonResponse
    {
        $appointment = $this->appointmentService->getAppointment($id);
        $this->authorize('update', $appointment);

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
        $appointment = $this->appointmentService->getAppointment($id);
        $this->authorize('delete', $appointment);

        $this->appointmentService
            ->deleteAppointment($id);

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully.'
        ], Response::HTTP_OK);
    }

    public function availableSlots(AvailableSlotsRequest $request): JsonResponse
    {
        $slots = $this->appointmentService->getAvailableSlots(
            $request->input('doctor_id'),
            $request->input('date')
        );

        return response()->json([
            'success' => true,
            'data' => $slots
        ], Response::HTTP_OK);
    }

    public function changeStatus(ChangeAppointmentStatusRequest $request, int $appointment): JsonResponse
    {
        $appointmentModel = $this->appointmentService->getAppointment($appointment);
        $status = $request->validated()['status'];
        $this->authorize('changeStatus', [$appointmentModel, $status]);

        return response()->json([
            'success' => true,
            'data' => $this->appointmentService
                ->changeStatus(
                    $appointment,
                    $status
                )
        ], Response::HTTP_OK);
    }
}
