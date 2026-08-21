<?php

namespace App\Http\Controllers;
use App\Models\Pharmacist;
use App\Http\Requests\StorePharmacistRequest;
use App\Http\Requests\UpdatePharmacistRequest;
use App\Services\PharmacistService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PharmacistController extends Controller
{
    protected PharmacistService $pharmacistService;

    public function __construct(PharmacistService $pharmacistService)
    {
        $this->pharmacistService = $pharmacistService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Pharmacist::class);
        $pharmacists = $this->pharmacistService->getAllPharmacists();
        return response()->json(['success' => true, 'data' => $pharmacists], Response::HTTP_OK);
    }


    public function store(StorePharmacistRequest $request): JsonResponse
    {
        $this->authorize('create', Pharmacist::class);
        $pharmacist = $this->pharmacistService->createPharmacist($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Pharmacist created successfully.',
            'data'    => $pharmacist
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $pharmacist = $this->pharmacistService->getPharmacistById($id);
        $this->authorize('view', $pharmacist);

        return response()->json(['success' => true, 'data' => $pharmacist], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePharmacistRequest $request, int $id): JsonResponse
    {
        $pharmacist = $this->pharmacistService->getPharmacistById($id);
        $this->authorize('update', $pharmacist);
        $this->pharmacistService->updatePharmacist($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Pharmacist records updated successfully.'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {   
        $pharmacist = $this->pharmacistService->getPharmacistById($id);
        $this->authorize('delete', $pharmacist);
        $this->pharmacistService->deletePharmacist($id);
        return response()->json([
            'success' => true,
            'message' => 'Pharmacist record deleted successfully.'
        ], Response::HTTP_OK);
    }
}