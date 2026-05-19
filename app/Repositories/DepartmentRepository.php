<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository
{
    public function all(): Collection
    {
        return Department::all();
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
}
