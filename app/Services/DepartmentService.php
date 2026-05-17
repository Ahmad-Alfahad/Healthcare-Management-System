<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentService
{
    protected $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function getAllDepartments(): Collection
    {
        return $this->departmentRepository->all();
    }

    public function getDepartmentById(int $id): Department
    {
        return $this->departmentRepository->find($id);
    }

    public function createDepartment(array $data): Department
    {
        return $this->departmentRepository->create($data);
    }

    public function updateDepartment(int $id, array $data): bool
    {
        return $this->departmentRepository->update($id, $data);
    }

    public function deleteDepartment(int $id): bool
    {
        return $this->departmentRepository->delete($id);
    }
}
