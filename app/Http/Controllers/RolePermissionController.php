<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermissionRequest;
use App\Models\User;
use App\Services\RolePermissionService;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionController extends Controller
{
    protected $service;

    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $accessData = $this->service->getAccessData();

        return response()->json([
            'success' => true,
            'message' => 'Access control data retrieved successfully.',
            'data'    => [
                'users'       => $accessData['users'],
                'roles'       => $accessData['roles'],
                'permissions' => $accessData['permissions'],
            ]
        ], Response::HTTP_OK); // 200
    }

    public function syncUserAccess(RolePermissionRequest $request, User $user)
    {
        $this->authorize('managePermissions', $user);
        $updatedUser = $this->service->syncUserAccess($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => "Access privileges synced successfully for user: {$updatedUser->name}.",
            'data'    => [
                'user_id'     => $updatedUser->id,
                'name'        => $updatedUser->name,
                'roles'       => $updatedUser->roles->pluck('name'),
                'permissions' => $updatedUser->permissions->pluck('name'),
            ]
        ], Response::HTTP_OK);
    }
}
