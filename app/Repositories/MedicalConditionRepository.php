<?php

namespace App\Repositories;

use App\Models\MedicalCondition;
use App\Support\ListQuery;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicalConditionRepository
{
    use ListQuery;

    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->paginateList(MedicalCondition::query(), $filters, ['name', 'type', 'description', 'status']);
    }

    public function find(int $id): MedicalCondition
    {
        return MedicalCondition::findOrFail($id);
    }

    public function create(array $data): MedicalCondition
    {
        return MedicalCondition::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $medical_condition = MedicalCondition::findOrFail($id);
        return $medical_condition->update($data);
    }

    public function delete(int $id): bool
    {
        $medical_condition = MedicalCondition::findOrFail($id);
        return $medical_condition->delete();
    }

    public function existsByNameAndType(string $name, string $type): bool
    {
        return MedicalCondition::where(
            'name',
            $name
        )
            ->where(
                'type',
                $type
            )
            ->exists();
    }

    public function existsByNameAndTypeExcept(string $name, string $type, int $id): bool
    {
        return MedicalCondition::where(
            'name',
            $name
        )
            ->where(
                'type',
                $type
            )
            ->where(
                'id',
                '!=',
                $id
            )
            ->exists();
    }

    public function hasPatientMedicalConditions(int $id): bool
    {
        return MedicalCondition::whereKey($id)
            ->whereHas('patientMedicalConditions')
            ->exists();
    }
}
