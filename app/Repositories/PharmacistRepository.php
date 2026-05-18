<?php

namespace App\Repositories;

use App\Models\Pharmacist;
use Illuminate\Database\Eloquent\Collection;

class PharmacistRepository
{
    public function all(): Collection
    {
        return Pharmacist::with(['profile', 'facility'])->get();
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