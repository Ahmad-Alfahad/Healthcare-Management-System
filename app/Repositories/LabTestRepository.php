<?php

namespace App\Repositories;

use App\Models\LabTest;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class LabTestRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(LabTest::query(), $filters, ['name', 'description', 'status']);
    }

    public function find(int $id): LabTest
    {
        return LabTest::findOrFail($id);
    }

    public function create(array $data): LabTest
    {
        return LabTest::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $lab_test = LabTest::findOrFail($id);
        return $lab_test->update($data);
    }

    public function delete(int $id): bool
    {
        $lab_test = LabTest::findOrFail($id);
        return $lab_test->delete();
    }

    public function existsByName(string $name): bool
    {
        return LabTest::where(
            'name',
            $name
        )->exists();
    }

    public function existsByNameExcept(string $name, int $id): bool
    {
        return LabTest::where(
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

    public function hasRequests(int $id): bool
    {
        return LabTest::whereKey($id)
            ->whereHas('labRequestItems')
            ->exists();
    }
}
