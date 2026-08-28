<?php

namespace App\Services;

use App\Repositories\RolePermissionRepository;
use App\Models\User;

class RolePermissionService
{
    protected RolePermissionRepository $repository;

    public function __construct(RolePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAccessData(?string $role = null, ?string $search = null)
    {
        return [
            'users' => $this->repository->getUsersWithRoles($role, $search),
            'roles' => $this->repository->getRoles(),
        ];
    }

    public function syncUserRoles(User $user, array $roles)
    {
        $this->repository->syncRoles($roles, $user);
        return $user->load('roles');
    }

    public function getRolesList()
    {
        return $this->repository->getRoles()->pluck('name', 'id');
    }
}
