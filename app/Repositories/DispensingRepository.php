<?php

namespace App\Repositories;

use App\Models\Dispensing;
use App\Models\User;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class DispensingRepository
{
    use ListQuery;

    public function all(?User $user = null, array $filters = []): LengthAwarePaginator
    {
        $query = Dispensing::with(['prescriptionItem', 'pharmacist']);
        if ($user?->isManager()) {
            $query->whereHas('prescriptionItem.prescription.visit.doctor.facilityDepartmentSpecialization.facilityDepartment', fn($q) => $q->where('facility_id', $user->facility()?->id));
        }
        return $this->paginateList($query, $filters, ['quantity_dispensed', 'dispensed_at'], ['prescriptionItem' => ['medication_name'], 'pharmacist.profile' => ['full_name']]);
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
