<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePermissionRepository
{

    public function getUserWithRoles()
    {
        return User::with(['roles'])

            ->latest()
            ->paginate(10);
    }

    public function getRoles()
    {
        return Role::all();
    }

    public function syncRole(array $data, User $user)
    {
        $user->syncRoles($data['roles'] ?? []);

        return $user;
    }


}
