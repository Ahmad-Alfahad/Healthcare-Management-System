<?php

namespace App\Repositories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Collection;

class FacilityRepository
{
    public function all(): Collection
    {
        return Facility::with('parent')->get();
    }

    public function find(int $id): ?Facility
    {
        return Facility::with('children')->findOrFail($id);
    }

    public function create(array $data): Facility
    {
        return Facility::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $facility = Facility::findOrFail($id);
        return $facility->update($data);
    }

    public function delete(int $id): bool
    {
        $facility = Facility::findOrFail($id);
        return $facility->delete();
    }
}
