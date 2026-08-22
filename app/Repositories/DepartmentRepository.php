<?php

namespace App\Repositories;

use App\Models\Department;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(Department::query(), $filters, ['name', 'description', 'status']);
    }

    public function find(int $id): Department
    {
        return Department::findOrFail($id);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $department = Department::findOrFail($id);
        return $department->update($data);
    }

    public function delete(int $id): bool
    {
        $department = Department::findOrFail($id);
        return $department->delete();
    }

    public function existsByName(string $name): bool
    {
        return Department::where(
            'name',
            $name
        )->exists();
    }

    public function existsByNameExcept(string $name, int $id): bool
    {
        return Department::where(
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
