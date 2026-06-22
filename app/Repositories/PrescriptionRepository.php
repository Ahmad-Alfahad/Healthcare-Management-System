<?php

namespace App\Repositories;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionRepository
{
    public function all(): Collection
    {
        return Prescription::with('visit')->get();
    }

    public function find(int $id): Prescription
    {
        return Prescription::with(['visit' , 'items'])->findOrFail($id);
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
