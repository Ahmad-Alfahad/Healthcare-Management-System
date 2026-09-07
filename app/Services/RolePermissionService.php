<?php

namespace App\Services;

use App\Repositories\RolePermissionRepository;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RolePermissionService
{
    protected RolePermissionRepository $repository;

    public function __construct(RolePermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAccessData(User $actor, ?string $role = null, ?string $search = null)
    {
        return [
            'users' => $this->repository->getUsersWithRoles($actor, $role, $search),
            'roles' => $this->getRolesList($actor),
        ];
    }

    public function syncUserRoles(User $actor, User $user, array $roles)
    {
        $this->validateManagerAssignment($user, $roles);

        if ($actor->isManager()) {
            if (!$this->canManageUser($actor, $user)) {
                abort(403, 'Managers can only manage employees in their facilities.');
            }

            $allowedRoles = ['doctor', 'pharmacist', 'laboratory'];
            if (array_diff($roles, $allowedRoles) !== []) {
                throw ValidationException::withMessages([
                    'roles' => ['Managers may assign only professional employee roles.'],
                ]);
            }
        }

        $this->repository->syncRoles($roles, $user);
        return $user->load('roles');
    }

    public function getRolesList(User $actor)
    {
        $roles = $this->repository->getRoles();

        if ($actor->isManager()) {
            $roles = $roles->whereIn('name', ['doctor', 'pharmacist', 'laboratory']);
        }

        return $roles->pluck('name', 'id');
    }

    private function canManageUser(User $actor, User $target): bool
    {
        return $target->profile?->employee !== null
            && in_array(
                $target->profile->employee->facility_id,
                $actor->accessibleFacilityIds(),
                true
            );
    }

    private function validateManagerAssignment(User $target, array $roles): void
    {
        if (!in_array('manager', $roles, true) || $target->hasRole('manager')) {
            return;
        }

        $facility = $target->profile?->employee?->facility;

        if ($facility === null) {
            throw ValidationException::withMessages([
                'roles' => ['Only an employee assigned to a facility can be a manager.'],
            ]);
        }

        $hasManager = User::role('manager')
            ->where('id', '!=', $target->id)
            ->whereHas('profile.employee', function ($employeeQuery) use ($facility) {
                $employeeQuery->whereIn('facility_id', $facility->familyIds());
            })
            ->exists();

        if ($hasManager) {
            throw ValidationException::withMessages([
                'roles' => ['This facility family already has a manager.'],
            ]);
        }
    }
}
