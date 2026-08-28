<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePermissionRepository
{

    public function getUsersWithRoles(?string $role = null, ?string $search = null)
    {
        return User::with(['roles' , 'profile'])
            ->when($role, function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);
    }

 public function getRoles()
    {
        return Role::select('id', 'name')->get();
    }

    public function syncRoles(array $roles, User $user)
    {
        $user->syncRoles($roles);
        return $user;
    }
}
