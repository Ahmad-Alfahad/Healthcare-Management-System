<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function currentUser(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->userService->currentUser($request->user()),
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => 'required|string|max:255|regex:/^[\pL\s]+$/u',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|numeric|digits_between:8,15',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'national_number' => 'nullable|string|unique:profiles,national_number|numeric|digits_between:8,15',
            'date_of_birth' => 'nullable|date|before:today',
        ]);

        $response = $this->userService->register($payload);

        return response()->json($response);
    }

    public function login(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $response = $this->userService->login($payload);

        return response()->json($response);
    }

    public function logout(Request $request): JsonResponse
    {
        $response = $this->userService->logout($request->user());

        return response()->json($response);
    }
}
