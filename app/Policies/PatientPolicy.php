<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement();
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->isManagement()) {
            return true;
        }

        return $patient->profile->belongsToUser($user);
    }

    public function create(User $user): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, Patient $patient): bool
    {
      return $user->isManagement();
    }

    public function delete(User $user, Patient $patient): bool
    {
        return false;
    }
}