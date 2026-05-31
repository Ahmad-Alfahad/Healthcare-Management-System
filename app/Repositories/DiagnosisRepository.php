<?php

namespace App\Repositories;
use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Collection;

class DiagnosisRepository
{
    public function all(): Collection
    {
        return Diagnosis::all();
    }

    public function find(int $id): Diagnosis
    {
        return Diagnosis::findOrFail($id);
    }

    public function create(array $data): Diagnosis
    {
        return Diagnosis::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $diagnosis = Diagnosis::findOrFail($id);
        return $diagnosis->update($data);
    }

    public function delete(int $id): bool
    {
        $diagnosis = Diagnosis::findOrFail($id);
        return $diagnosis->delete();
    }
}