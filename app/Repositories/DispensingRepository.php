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
        $query = Dispensing::with([
            'prescriptionItem.prescription.visit.patient.profile',
            'prescriptionItem.prescription.visit.doctor.profile',
            'pharmacist',
            'pharmacist.profile',
        ]);

        if ($user?->isAdmin()) {
            return $this->paginateList($query, $filters, ['quantity_dispensed', 'dispensed_at'], ['prescriptionItem' => ['medication_name'], 'pharmacist.profile' => ['full_name']]);
        }

        if ($user?->isDoctor()) {
            $query->whereHas(
                'prescriptionItem.prescription.visit',
                fn($visitQuery) => $visitQuery->where('doctor_id', $user->doctor?->id)
            );
        } elseif ($user?->isPatient()) {
            $query->whereHas(
                'prescriptionItem.prescription.visit',
                fn($visitQuery) => $visitQuery->where('patient_id', $user->patient?->id)
            );
        } elseif ($user?->isManager()) {
            $query->whereHas(
                'prescriptionItem.prescription.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment',
                fn($facilityDepartmentQuery) => $facilityDepartmentQuery->where(
                    'facility_id',
                    $user->facility()?->id
                )
            );
        } elseif ($user?->isPharmacist()) {
            $query->whereHas(
                'prescriptionItem.prescription.visit.appointment.doctor.facilityDepartmentSpecialization.facilityDepartment',
                fn($facilityDepartmentQuery) => $facilityDepartmentQuery->whereIn(
                    'facility_id',
                    $user->accessibleFacilityIds()
                )
            );
        } else {
            $query->whereRaw('1 = 0');
        }

        return $this->paginateList($query, $filters, ['quantity_dispensed', 'dispensed_at'], ['prescriptionItem' => ['medication_name'], 'pharmacist.profile' => ['full_name']]);
    }

    public function find(int $id): Dispensing
    {
        return Dispensing::with(['prescriptionItem', 'pharmacist', 'pharmacist.profile'])->findOrFail($id);
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
