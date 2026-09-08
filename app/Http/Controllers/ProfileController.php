<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Profile::class);
        $profiles = $this->profileService->getAll(request()->user());

        return response()->json([
            'success' => true,
            'message' => 'Profiles list retrieved successfully.',
            'data' => $profiles->toArray(),
        ], Response::HTTP_OK);
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $this->authorize('create', Profile::class);
        $profile = $this->profileService->createProfile($request->validated());
        $profile->load(['user.roles', 'patient', 'employee.doctor', 'employee.pharmacist', 'employee.labStaff']);

        return response()->json([
            'success' => true,
            'message' => 'Profile created successfully.',
            'data' => $profile->toArray(),
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $profile = $this->profileService->getProfileById($id);
        $this->authorize('view', $profile);

        return response()->json([
            'success' => true,
            'message' => 'Profile details retrieved successfully.',
            'data' => $profile->toArray(),
        ], Response::HTTP_OK);
    }

    public function update(UpdateProfileRequest $request, int $id): JsonResponse
    {
        $profile = $this->profileService->getProfileById($id);
        $this->authorize('update', $profile);
        $profile = $this->profileService->update($id, $request->validated());
        $profile->load(['user.roles']);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $profile = $this->profileService->getProfileById($id);
        $this->authorize('delete', $profile);
        $this->profileService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Profile deleted successfully.',
        ], Response::HTTP_OK);
    }
}
