<?php

namespace App\Repositories;

use App\Models\LabStaff;
use Illuminate\Database\Eloquent\Collection;

class LabStaffRepository
{
    public function all(): Collection
    {
        return LabStaff::with(['profile', 'facility'])->get();
    }

    public function getByFacility(int $facilityId): Collection
    {
        return LabStaff::with(['profile', 'facility'])
            ->where('facility_id', $facilityId)
            ->get();
    }

    public function find(int $id): LabStaff
    {
        return LabStaff::with(['profile', 'facility'])->findOrFail($id);
    }

    public function create(array $data): LabStaff
    {
        return LabStaff::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $staff = LabStaff::findOrFail($id);
        return $staff->update($data);
    }

    public function delete(int $id): bool
    {
        $staff = LabStaff::findOrFail($id);
        return $staff->delete();
    }
}
