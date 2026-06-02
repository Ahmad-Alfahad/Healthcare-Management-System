<?php
namespace App\Repositories;

use App\Models\LabTest;
use Illuminate\Database\Eloquent\Collection;

class LabTestRepository
{
    public function all(): Collection
    {
        return LabTest::all();
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
}