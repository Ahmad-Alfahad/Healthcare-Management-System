<?php

namespace App\Repositories;

use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionItemRepository
{
    public function all(?User $user = null): Collection
    {
        $query = PrescriptionItem::with('prescription');
        if ($user?->isManager()) {
            $query->whereHas('prescription.visit.doctor.facilityDepartmentSpecialization.facilityDepartment', fn($q) => $q->where('facility_id', $user->facility()?->id));
        }
        return $query->get();
    }

    public function find(int $id): PrescriptionItem
    {
        return PrescriptionItem::with('prescription')->findOrFail($id);
    }

    public function create(array $data): PrescriptionItem
    {
        return PrescriptionItem::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $item = PrescriptionItem::findOrFail($id);
        return $item->update($data);
    }

    public function delete(int $id): bool
    {
        $item = PrescriptionItem::findOrFail($id);
        return $item->delete();
    }

    public function existsMedicationInPrescription(int $prescriptionId, string $medicationName): bool
    {
        return PrescriptionItem::where(
            'prescription_id',
            $prescriptionId
        )
            ->where(
                'medication_name',
                $medicationName
            )
            ->exists();
    }

    public function existsMedicationInPrescriptionExcept(int $prescriptionId, string $medicationName, int $itemId): bool
    {
        return PrescriptionItem::where(
            'prescription_id',
            $prescriptionId
        )
            ->where(
                'medication_name',
                $medicationName
            )
            ->where(
                'id',
                '!=',
                $itemId
            )
            ->exists();
    }
}
