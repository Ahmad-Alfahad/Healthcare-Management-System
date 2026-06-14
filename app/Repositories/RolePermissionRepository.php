<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionRepository
{

    public function getUserWithRolesAndPermissions()
    {
        return User::with(['roles', 'permissions'])

            ->latest()
            ->paginate(10);
    }


    public function getRole()
    {
        return Role::all();
    }


    public function getPermission()
    {
        return Permission::all();
    }

    public function syncRole(array $data, User $user)
    {
        $user->syncRoles($data['roles'] ?? []);

        $this->clearPermissionCache();

        return $user;
    }

    public function syncPermission(array $data, User $user)
    {
        $user->syncPermissions($data['permissions'] ?? []);

        $this->clearPermissionCache();

        return $user;
    }

  
    protected function clearPermissionCache()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
