<?php

namespace App\Repositories;

use App\Models\LabStaff;
use Illuminate\Database\Eloquent\Collection;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class LabStaffRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(LabStaff::with(['profile', 'facility']), $filters, ['license_number'], ['profile' => ['full_name'], 'facility' => ['name']], ['facility_id' => 'facility_id']);
    }

    public function getByFacility(int $facilityId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(LabStaff::with(['profile', 'facility'])->where('facility_id', $facilityId), $filters, ['license_number'], ['profile' => ['full_name'], 'facility' => ['name']]);
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
