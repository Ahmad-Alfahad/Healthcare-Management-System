<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\PatientMedicalCondition;
use App\Models\User;

class PatientMedicalConditionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManagement()
            || $user->isStaff()
            || $user->isPatient();
    }

    public function view(User $user, PatientMedicalCondition $patientMedicalCondition): bool
    {
        $patient = $patientMedicalCondition->patient;

        if ($patient === null) {
            return false;
        }

        if ($user->isManagement()) {
            return true;
        }

        if ($user->isPatient()) {
            return $user->patient?->id === $patient->id;
        }

        return $user->isStaff();
    }

    public function create(User $user, Patient $patient): bool
    {
        return $user->isManagement();
    }

    public function update(User $user, PatientMedicalCondition $patientMedicalCondition): bool
    {
        return $user->isManagement();
    }

    public function delete(User $user, PatientMedicalCondition $patientMedicalCondition): bool
    {
        return $user->isManagement();
    }
}
