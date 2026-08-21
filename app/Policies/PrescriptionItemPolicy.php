<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;

class PrescriptionItemPolicy
{
    public function viewAny(User $user): bool
    {
        return (new PrescriptionPolicy())->viewAny($user);
    }

    public function view(User $user, PrescriptionItem $item): bool
    {
        return (new PrescriptionPolicy())->view($user, $item->prescription);
    }

    public function create(User $user, Prescription $prescription): bool
    {
        return (new PrescriptionPolicy())->update($user, $prescription);
    }

    public function update(User $user, PrescriptionItem $item): bool
    {
        return (new PrescriptionPolicy())->update($user, $item->prescription);
    }

    public function delete(User $user, PrescriptionItem $item): bool
    {
        return (new PrescriptionPolicy())->delete($user, $item->prescription);
    }
}
