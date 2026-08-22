<?php

namespace App\Http\Controllers;

use App\Models\Pharmacist;
use App\Models\Facility;
use App\Http\Requests\StorePharmacistRequest;
use App\Http\Requests\UpdatePharmacistRequest;
use App\Services\PharmacistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PharmacistController extends Controller
{
    protected PharmacistService $pharmacistService;

    public function __construct(PharmacistService $pharmacistService)
    {
        $this->pharmacistService = $pharmacistService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Pharmacist::class);
        $filters = $request->validate(['search' => ['sometimes', 'string', 'max:255'], 'page' => ['sometimes', 'integer', 'min:1'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']]);
        $pharmacists = $this->pharmacistService->getAllPharmacists(request()->user(), $filters);
        return response()->json(['success' => true, 'data' => $pharmacists], Response::HTTP_OK);
    }


    public function store(StorePharmacistRequest $request): JsonResponse
    {
        $data = $request->validated();
        $facility = Facility::findOrFail($data['facility_id']);
        $this->authorize('create', [Pharmacist::class, $facility]);
        $pharmacist = $this->pharmacistService->createPharmacist($data);
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
        $data = $request->validated();
        $this->authorize('update', $pharmacist);
        if (request()->user()->isManager() && isset($data['facility_id'])) {
            abort_unless(request()->user()->managesFacility(Facility::findOrFail($data['facility_id'])), 403);
        }
        $this->pharmacistService->updatePharmacist($id, $data);
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
