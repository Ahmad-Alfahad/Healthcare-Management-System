<?php

namespace App\Services;

use App\Repositories\RolePermissionRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    protected $repository;

    public function __construct(RolePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAccessData()
    {
        return [
            'users'       => $this->repository->getUserWithRolesAndPermissions(),
            'roles'       => $this->repository->getRole(),
            'permissions' => $this->repository->getPermission(),
        ];
    }

    public function syncUserAccess(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $this->repository->syncRole($data, $user);
            $this->repository->syncPermission($data, $user);

            // إعادة شحن العلاقات للتأكد من إرجاع البيانات المحدثة للـ Controller
            return $user->load(['roles', 'permissions']);
        });
    }
}
