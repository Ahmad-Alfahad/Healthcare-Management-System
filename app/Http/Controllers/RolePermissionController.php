<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermissionRequest;
use App\Models\User;
use App\Services\RolePermissionService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    protected RolePermissionService $service;

    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $selectedRole = $request->query('role');
        $searchTerm   = $request->query('search'); 
        $accessData = $this->service->getAccessData($selectedRole, $searchTerm);

        return response()->json([
            'success' => true,
            'message' => 'Users and roles data retrieved successfully.',
            'data'    => $accessData
        ], Response::HTTP_OK);
    }

    public function syncUserAccess(RolePermissionRequest $request, User $user)
    {
        $this->authorize('managePermissions', $user);

        $updatedUser = $this->service->syncUserRoles($user, $request->validated('roles', []));

        return response()->json([
            'success' => true,
            'message' => "Roles updated successfully for user: {$updatedUser->name}.",
            'data'    => [
                'user_id' => $updatedUser->id,
                'name'    => $updatedUser->name,
                'roles'   => $updatedUser->roles->pluck('name'),
            ]
        ], Response::HTTP_OK);
    }

    public function getRoles()
    {
        $roles = $this->service->getRolesList();

        return response()->json([
            'success' => true,
            'data'    => $roles
        ], Response::HTTP_OK);
    }
}
