<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }


    // 1. READ (ALL)
    public function index()
    {
    

        $profiles = $this->profileService->listAllProfiles();
        dd($profiles); // تحقق من تحميل العلاقات
        return ProfileResource::collection($profiles)->additional([
            'success' => true,
            'message' => 'Profiles list retrieved successfully.'
        ]);
    }

    // 2. CREATE
    public function store(StoreProfileRequest $request)
    {
        $profile = $this->profileService->storeProfile($request->validated());
        return (new ProfileResource($profile))
            ->additional([
                'success' => true,
                'message' => 'Profile created successfully.'
            ])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED); // 201
    }

    // 3. READ (SINGLE)
    public function show(int $id)
    {
        $profile = $this->profileService->getProfileById($id);
        return response()->json([
            'success' => true,
            'message' => 'Profile details retrieved successfully.',
            'data'    => new ProfileResource($profile)
        ], Response::HTTP_OK); // 200
    }

    // 4. UPDATE
    public function update(UpdateProfileRequest $request, int $id)
    {
        $profile = $this->profileService->updateProfile($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => new ProfileResource($profile)
        ], Response::HTTP_OK); // 200
    }

    // 5. DELETE
    public function destroy(int $id)
    {
        $this->profileService->deleteProfile($id);
        return response()->json([
            'success' => true,
            'message' => 'Profile deleted successfully.'
        ], Response::HTTP_NO_CONTENT); // 204
    }
}
