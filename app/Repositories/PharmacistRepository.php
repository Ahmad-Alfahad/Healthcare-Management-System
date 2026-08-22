<?php

namespace App\Repositories;

use App\Models\Pharmacist;
use Illuminate\Database\Eloquent\Collection;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class PharmacistRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Pharmacist::with(['profile', 'facility']), $filters, ['license_number'], ['profile' => ['full_name'], 'facility' => ['name']], ['facility_id' => 'facility_id']);
    }

    public function getByFacility(int $facilityId, array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Pharmacist::with(['profile', 'facility'])->where('facility_id', $facilityId), $filters, ['license_number'], ['profile' => ['full_name'], 'facility' => ['name']]);
    }

    public function find(int $id): Pharmacist
    {
        return Pharmacist::with(['profile', 'facility'])->findOrFail($id);
    }

    public function create(array $data): Pharmacist
    {
        return Pharmacist::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $pharmacist = Pharmacist::findOrFail($id);
        return $pharmacist->update($data);
    }

    public function delete(int $id): bool
    {
        $pharmacist = Pharmacist::findOrFail($id);
        return $pharmacist->delete();
    }
}
