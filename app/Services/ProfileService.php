<?php

namespace App\Services;

use App\Repositories\ProfileRepository;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Validation\ValidationException;


class ProfileService
{
    protected ProfileRepository $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function getAll(?User $user = null)
    {
        return $this->profileRepository->getAll($user);
    }

    public function getProfileById(int $id)
    {
        $profile = $this->profileRepository->findById($id);

        return $profile->load([
            'user.roles',
        ]);
    }

    public function createProfile(array $data): Profile
    {
        return $this->profileRepository->create(
            $data
        );
    }

    public function update(int $id, array $data): ?Profile
    {
        $profile =
            $this->profileRepository->findById($id);

        $this->profileRepository->update(
            $profile,
            $data
        );

        return $profile->fresh();
    }

    public function delete(int $id): bool
    {
        $profile =
            $this->profileRepository->findById($id);

        $this->validateCanDelete($profile);

        return $this->profileRepository->delete(
            $profile
        );
    }

    private function validateCanDelete(Profile $profile): void
    {
        if ($profile->patient) {

            throw ValidationException::withMessages([
                'profile' => [
                    'Profile is assigned to a patient.'
                ]
            ]);
        }

        if ($profile->doctor) {

            throw ValidationException::withMessages([
                'profile' => [
                    'Profile is assigned to a doctor.'
                ]
            ]);
        }

        if ($profile->pharmacist) {

            throw ValidationException::withMessages([
                'profile' => [
                    'Profile is assigned to a pharmacist.'
                ]
            ]);
        }

        if ($profile->labStaff) {

            throw ValidationException::withMessages([
                'profile' => [
                    'Profile is assigned to a lab staff member.'
                ]
            ]);
        }
    }
}
