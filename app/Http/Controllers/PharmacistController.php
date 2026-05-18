<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePharmacistRequest;
use App\Http\Requests\UpdatePharmacistRequest;
use App\Services\PharmacistService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PharmacistController extends Controller
{
    protected $pharmacistService;

    public function __construct(PharmacistService $pharmacistService)
    {
        $this->pharmacistService = $pharmacistService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $pharmacists = $this->pharmacistService->getAllPharmacists();
        return response()->json(['success' => true, 'data' => $pharmacists], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePharmacistRequest $request): JsonResponse
    {
        $pharmacist = $this->pharmacistService->createPharmacist($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Pharmacist created successfully.',
            'data'    => $pharmacist
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $pharmacist = $this->pharmacistService->getPharmacistById($id);
        return response()->json(['success' => true, 'data' => $pharmacist], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePharmacistRequest $request, int $id): JsonResponse
    {
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
        $this->pharmacistService->deletePharmacist($id);
        return response()->json([
            'success' => true,
            'message' => 'Pharmacist record deleted successfully.'
        ], Response::HTTP_OK);
    }
}