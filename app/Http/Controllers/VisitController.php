<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Http\Requests\ChangeVisitStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\VisitService;
use App\Models\Appointment;
use App\Models\Visit;


class VisitController extends Controller
{

    protected VisitService $visitService;

    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Visit::class);
        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'page' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'status' => ['sometimes', 'string'], 'from' => ['sometimes', 'date'], 'to' => ['sometimes', 'date', 'after_or_equal:from']]);
        $visits = $this->visitService->getAllVisits(request()->user(), $filters);
        return response()->json(['success' => true, 'data' => $visits], Response::HTTP_OK);
    }

    public function store(StoreVisitRequest $request): JsonResponse
    {
        $appointment = Appointment::findOrFail(
            $request->validated()['appointment_id']
        );
        $this->authorize('create', [Visit::class, $appointment]);
        $visit = $this->visitService->createVisit($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Visit created successfully.',
            'data'    => $visit
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $visit = $this->visitService->getVisitById($id);
        $this->authorize('view', $visit);
        return response()->json(['success' => true, 'data' => $visit], Response::HTTP_OK);
    }

    public function update(UpdateVisitRequest $request, int $id): JsonResponse
    {
        $visit = $this->visitService->getVisitById($id);
        $this->authorize('update', $visit);
        $this->visitService->updateVisit($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Visit updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $visit = $this->visitService->getVisitById($id);
        $this->authorize('delete', $visit);
        $this->visitService->deleteVisit($id);
        return response()->json([
            'success' => true,
            'message' => 'Visit deleted successfully.'
        ], Response::HTTP_OK);
    }

    public function changeStatus(ChangeVisitStatusRequest $request, int $id): JsonResponse
    {
        $visit = $this->visitService->getVisitById($id);
        $this->authorize('changeStatus', $visit);
        $this->visitService->changeStatus(
            $id,
            $request->validated()['status']
        );

        return response()->json([
            'success' => true,
            'message' => 'Visit status updated successfully.'
        ]);
    }
}
