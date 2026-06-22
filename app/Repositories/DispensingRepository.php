<?php

namespace App\Repositories;

use App\Models\Dispensing;
use Illuminate\Database\Eloquent\Collection;

class DispensingRepository
{
    public function all(): Collection
    {
        return Dispensing::with(['prescriptionItem', 'pharmacist'])->get();
    }

    public function find(int $id): Dispensing
    {
        return Dispensing::with(['prescriptionItem', 'pharmacist'])->findOrFail($id);
    }

    public function create(array $data): Dispensing
    {
        return Dispensing::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $dispensing = Dispensing::findOrFail($id);
        return $dispensing->update($data);
    }

    public function delete(int $id): bool
    {
        $dispensing = Dispensing::findOrFail($id);
        return $dispensing->delete();
    }

    public function getTotalDispensedForItem(int $prescriptionItemId): int
    {
        return Dispensing::where(
            'prescription_item_id',
            $prescriptionItemId
        )->sum('quantity_dispensed');
    }
}
