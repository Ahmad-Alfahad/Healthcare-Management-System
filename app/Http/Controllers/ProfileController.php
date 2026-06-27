<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
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
        $profiles = $this->profileService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Profiles list retrieved successfully.',
            'data' => $profiles->toArray(),
        ], Response::HTTP_OK);
    }

    public function store(StoreProfileRequest $request): JsonResponse
    {
        $profile = $this->profileService->createProfile($request->validated());
        $profile->load(['user.roles', 'patient', 'doctor', 'pharmacist', 'labStaff']);

        return response()->json([
            'success' => true,
            'message' => 'Profile created successfully.',
            'data' => $profile->toArray(),
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $profile = $this->profileService->getProfileById($id);

        return response()->json([
            'success' => true,
            'message' => 'Profile details retrieved successfully.',
            'data' => $profile->toArray(),
        ], Response::HTTP_OK);
    }

    public function update(UpdateProfileRequest $request, int $id): JsonResponse
    {
        $profile = $this->profileService->update($id, $request->validated());
        $profile->load(['user.roles']);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->profileService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Profile deleted successfully.',
        ], Response::HTTP_OK);
    }
}
