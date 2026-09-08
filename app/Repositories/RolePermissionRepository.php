<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePermissionRepository
{

    public function getUsersWithRoles(User $actor, ?string $role = null, ?string $search = null)
    {
        return User::with(['roles' , 'profile'])
            ->when($actor->isManager(), function ($query) use ($actor) {
                $query->whereHas('profile.employee', function ($employeeQuery) use ($actor) {
                    $employeeQuery->whereIn('facility_id', $actor->accessibleFacilityIds());
                });
            })
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
        return Role::select('id', 'name')
        ->where('name' , '!=' , 'admin')
        ->get();
    }

    public function syncRoles(array $roles, User $user)
    {
        $user->syncRoles($roles);
        return $user;
    }
}
