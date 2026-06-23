<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DepartmentService
{
    protected  DepartmentRepository $departmentRepository;

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
        $this->validateDepartmentUniqueness($data['name']);
        return $this->departmentRepository->create($data);
    }

    public function updateDepartment(int $id, array $data): bool
    {
        if (isset($data['name'])) {
            $this->validateDepartmentUniquenessUpdate($id, $data['name']);
        }
        return $this->departmentRepository->update($id, $data);
    }

    public function deleteDepartment(int $id): bool
    {
        return $this->departmentRepository->delete($id);
    }

    // public function deactivate(int $id): ?Department
    // {
    //     $department =
    //         $this->departmentRepository->find($id);

    //     $this->departmentRepository->update(
    //         $id,
    //         [
    //             'is_active' => false
    //         ]
    //     );

    //     return $department->fresh();
    // }

    private function validateDepartmentUniqueness(string $name): void
    {
        if (
            $this->departmentRepository
            ->existsByName($name)
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Department already exists.'
                ]
            ]);
        }
    }

    private function validateDepartmentUniquenessUpdate(int $id, string $name): void
    {
        if (
            $this->departmentRepository
            ->existsByNameExcept(
                $name,
                $id
            )
        ) {
            throw ValidationException::withMessages([
                'name' => [
                    'Department already exists.'
                ]
            ]);
        }
    }
}
