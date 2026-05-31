<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\VisitService;

class VisitController extends Controller
{
   
    protected $visitService;
    
    public function __construct(VisitService $visitService)
    {
        $this->visitService = $visitService;
    }

    public function index(): JsonResponse
    {
        $visits = $this->visitService->getAllVisits();
        return response()->json(['success' => true, 'data' => $visits], Response::HTTP_OK);
    }

    public function store(StoreVisitRequest $request): JsonResponse
    {
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
        return response()->json(['success' => true, 'data' => $visit], Response::HTTP_OK);
    }

    public function update(UpdateVisitRequest $request, int $id): JsonResponse
    {
        $this->visitService->updateVisit($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Visit updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->visitService->deleteVisit($id);
        return response()->json([
            'success' => true,
            'message' => 'Visit deleted successfully.'
        ], Response::HTTP_OK);
    }
}

