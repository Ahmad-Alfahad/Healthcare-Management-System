<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserService
{
    protected UserRepository $userRepository;
    protected DatabaseManager $db;

    public function __construct(UserRepository $userRepository, DatabaseManager $db)
    {
        $this->userRepository = $userRepository;
        $this->db = $db;
    }

    public function register(array $data): array
    {
        return $this->db->transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $user->assignRole('patient');

            $profile = Profile::create([
                'user_id' => $user->id,
                'full_name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'national_number' => $data['national_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
            ]);

            Patient::create([
                'profile_id' => $profile->id,
            ]);

            return [
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
                'user' => $user->load('profile'),
            ];
        });
    }

    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['User account is inactive.'],
            ]);
        }

        return [
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user->load('profile'),
        ];
    }

    public function logout(User $user): array
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return ['message' => 'Logged out successfully.'];
    }
}
