<?php

namespace App\Policies;

use App\Models\LabStaff;
use App\Models\Facility;
use App\Models\User;

class LabStaffPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LabStaff $labStaff): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($labStaff->employee->facility))
            || $user->id === $labStaff->employee->profile->user_id;
    }

    public function create(User $user, Facility $facility): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($facility));
    }

    public function update(User $user, LabStaff $labStaff): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($labStaff->employee->facility));
    }

    public function delete(User $user, LabStaff $labStaff): bool
    {
        return $user->isAdmin()
            || ($user->isManager() && $user->managesFacility($labStaff->employee->facility));
    }
}