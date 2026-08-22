<?php

namespace App\Repositories;

use App\Models\Prescription;
use App\Models\User;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class PrescriptionRepository
{
    use ListQuery;

    public function all(?User $user = null, array $filters = []): LengthAwarePaginator
    {
        $query = Prescription::with('visit');
        if ($user?->isManager()) {
            $query->whereHas('visit.doctor.facilityDepartmentSpecialization.facilityDepartment', fn($q) => $q->where('facility_id', $user->facility()?->id));
        }
        return $this->paginateList($query, $filters, ['status'], ['visit.patient.profile' => ['full_name'], 'visit.doctor.profile' => ['full_name']]);
    }

    public function find(int $id): Prescription
    {
        return Prescription::with(['visit', 'items'])->findOrFail($id);
    }

    public function create(array $data): Prescription
    {
        return Prescription::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $prescription = Prescription::findOrFail($id);
        return $prescription->update($data);
    }

    public function delete(int $id): bool
    {
        $prescription = Prescription::findOrFail($id);
        return $prescription->delete();
    }

    public function existsByVisitId(int $visitId): bool
    {
        return Prescription::where(
            'visit_id',
            $visitId
        )->exists();
    }

    public function updateStatus(int $prescriptionId, string $status): bool
    {
        return Prescription::where(
            'id',
            $prescriptionId
        )->update([
            'status' => $status
        ]);
    }
}
