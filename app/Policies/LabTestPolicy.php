<?php

namespace App\Policies;

use App\Models\LabTest;
use App\Models\User;

class LabTestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LabTest $labTest): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, LabTest $labTest): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, LabTest $labTest): bool
    {
        return $user->isManagement();
    }
}
