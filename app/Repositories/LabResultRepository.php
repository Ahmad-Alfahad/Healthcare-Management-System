<?php
namespace App\Repositories;

use App\Models\LabResult;
use Illuminate\Database\Eloquent\Collection;

class LabResultRepository
{
    public function all(): Collection
    {
        return LabResult::with(['labRequestItem', 'labStaff'])->get();
    }

    public function find(int $id): LabResult
    {
        return LabResult::with(['labRequestItem', 'labStaff'])->find($id);
    }

    public function create(array $data): LabResult
    {
        return LabResult::create($data);
    }

    public function update(LabResult $labResult, array $data): bool
    {
        return $labResult->update($data);
    }

    public function delete(LabResult $labResult): bool
    {
        return $labResult->delete();
    }
}