<?php

namespace App\Services;

use App\Repositories\ProfileRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Profile;

class ProfileService
{
    protected $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function listAllProfiles()
    {
        return $this->profileRepository->getAll();
    }

    public function getProfileById(int $id)
    {
        $profile = $this->profileRepository->findById($id);
        return $profile->load(['user.roles']); 
    }

    public function storeProfile(array $data)
    {
        return DB::transaction(function () use ($data) {
            $profile = $this->profileRepository->create($data);
            $user = $profile->user;

            if ($user->hasRole('doctor')) {
                $user->doctor()->create([
                    'specialization_id' => $data['specialization_id'],
                    'facility_id'       => $data['facility_id'],
                ]);
            } elseif ($user->hasRole('patient')) {
                $user->patient()->create([
                    'blood_type' => $data['blood_type'],
                ]);
            } 

            return $profile;
        });
    }

    public function updateProfile(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $profile = $this->profileRepository->findById($id);
            $updatedProfile = $this->profileRepository->update($profile, $data);
            $user = $updatedProfile->user;

            if ($user->hasRole('doctor') && $user->doctor) {
                $user->doctor()->update([
                    'specialization_id' => $data['specialization_id'] ?? $user->doctor->specialization_id,
                    'facility_id'       => $data['facility_id'] ?? $user->doctor->facility_id,
                ]);
            } elseif ($user->hasRole('patient') && $user->patient) {
                $user->patient()->update([
                    'blood_type' => $data['blood_type'] ?? $user->patient->blood_type,
                ]);
            }

            return $updatedProfile;
        });
    }

    public function deleteProfile(int $id)
    {
        $profile = $this->profileRepository->findById($id);
        return $this->profileRepository->delete($profile);
    }
}