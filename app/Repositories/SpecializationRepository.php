<?php

namespace App\Repositories;

use App\Models\Specialization;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class SpecializationRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Specialization::query(), $filters, ['name', 'description', 'status']);
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

    public function existsByName(string $name): bool
    {
        return Specialization::where(
            'name',
            $name
        )->exists();
    }

    public function existsByNameExcept(string $name, int $id): bool
    {
        return Specialization::where(
            'name',
            $name
        )
            ->where(
                'id',
                '!=',
                $id
            )
            ->exists();
    }
}
