<?php

namespace App\Policies;

use App\Models\LabStaff;
use App\Models\User;

class LabStaffPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LabStaff $labStaff): bool
    {
        return $user->isManagement() || $user->id === $labStaff->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, LabStaff $labStaff): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, LabStaff $labStaff): bool
    {
        return false;
    }
}