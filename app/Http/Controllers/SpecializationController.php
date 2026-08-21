<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecializationRequest;
use App\Http\Requests\UpdateSpecializationRequest;
use App\Models\Specialization;
use App\Services\SpecializationService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SpecializationController extends Controller
{
    protected $specializationService;

    public function __construct(SpecializationService $specializationService)
    {
        $this->specializationService = $specializationService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Specialization::class);
        $specializations = $this->specializationService->getAllSpecializations();
        return response()->json(['success' => true, 'data' => $specializations], Response::HTTP_OK);
    }

    public function store(StoreSpecializationRequest $request): JsonResponse
    {
        $this->authorize('create', Specialization::class);
        $specialization = $this->specializationService->createSpecialization($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Specialization created successfully.',
            'data'    => $specialization
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $specialization = $this->specializationService->getSpecializationById($id);
        $this->authorize('view', $specialization);
        return response()->json(['success' => true, 'data' => $specialization], Response::HTTP_OK);
    }

    public function update(UpdateSpecializationRequest $request, int $id): JsonResponse
    {
        $specialization = $this->specializationService->getSpecializationById($id);
        $this->authorize('update', $specialization);
        $this->specializationService->updateSpecialization($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Specialization updated successfully.'
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $specialization = $this->specializationService->getSpecializationById($id);
        $this->authorize('delete', $specialization);
        $this->specializationService->deleteSpecialization($id);
        return response()->json([
            'success' => true,
            'message' => 'Specialization deleted successfully.'
        ], Response::HTTP_OK);
    }
}
