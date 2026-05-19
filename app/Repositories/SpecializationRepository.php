<?php

namespace App\Repositories;

use App\Models\Specialization;
use Illuminate\Database\Eloquent\Collection;

class SpecializationRepository
{
    public function all(): Collection
    {
        return Specialization::all();
    }

    public function find(int $id): Specialization
    {
        return Specialization::findOrFail($id);
    }

    public function create(array $data): Specialization
    {
        return Specialization::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $specialization = Specialization::findOrFail($id);
        return $specialization->update($data);
    }

    public function delete(int $id): bool
    {
        $specialization = Specialization::findOrFail($id);
        return $specialization->delete();
    }
}
