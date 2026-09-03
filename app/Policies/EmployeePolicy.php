<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Facility;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($employee->facility));
    }

    public function create(User $user, Facility $facility): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($facility));
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($employee->facility));
    }

    public function assign(
        User $user,
        Employee $employee,
        Facility $facility
    ): bool {
        return $user->isAdmin()
            || (
                $user->isManager()
                && $user->managesFacility($employee->facility)
                && $user->managesFacility($facility)
            );
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($employee->facility));
    }
}
