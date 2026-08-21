<?php

namespace App\Policies;

use App\Models\MedicalCondition;
use App\Models\User;

class MedicalConditionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MedicalCondition $medicalCondition): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, MedicalCondition $medicalCondition): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, MedicalCondition $medicalCondition): bool
    {
        return $user->isManagement();
    }
}
