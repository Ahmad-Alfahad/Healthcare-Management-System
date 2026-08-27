<?php

namespace App\Services;

use App\Repositories\RolePermissionRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    protected RolePermissionRepository $repository;

    public function __construct(RolePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAccessData()
    {
        return [
            'users'       => $this->repository->getUserWithRoles(),
            'roles'       => $this->repository->getRoles(),
        ];
    }

    public function syncUserAccess(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $this->repository->syncRole($data, $user);
            return $user->load(['roles']);
        });
    }

    public function getRolesList()
    {
        return $this->repository->getRoles()->pluck('name', 'id');
    }
}
