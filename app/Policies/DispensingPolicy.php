<?php

namespace App\Policies;

use App\Models\Dispensing;
use App\Models\PrescriptionItem;
use App\Models\User;

class DispensingPolicy
{
    public function viewAny(User $user): bool
    {
        return (new PrescriptionPolicy())->viewAny($user);
    }

    public function view(User $user, Dispensing $dispensing): bool
    {
        $prescription = $dispensing->prescriptionItem?->prescription;

        return $prescription !== null
            && (new PrescriptionPolicy())->view($user, $prescription);
    }

    public function create(
        User $user,
        PrescriptionItem $item,
        int $pharmacistId
    ): bool {
        if ($user->isAdmin()) {
            return true;
        }

        $prescription = $item->prescription;

        return $prescription !== null
            && $user->isPharmacist()
            && $user->pharmacist?->id === $pharmacistId
            && (new PrescriptionPolicy())->dispense($user, $prescription);
    }

    public function update(User $user, Dispensing $dispensing): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Dispensing $dispensing): bool
    {
        return $user->isAdmin();
    }
}
