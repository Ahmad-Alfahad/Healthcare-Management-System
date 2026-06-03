<?php
namespace App\Repositories;

use App\Models\PatientMedicalCondition;
use Illuminate\Database\Eloquent\Collection;

class PatientMedicalConditionRepository
{
    public function all(): Collection
    {
        return PatientMedicalCondition::all();
    }

    public function find(int $id): ?PatientMedicalCondition
    {
        return PatientMedicalCondition::find($id);
    }

    public function create(array $data): PatientMedicalCondition
    {
        return PatientMedicalCondition::create($data);
    }

    public function update(PatientMedicalCondition $patientMedicalCondition, array $data): bool
    {
        return $patientMedicalCondition->update($data);
    }

    public function delete(PatientMedicalCondition $patientMedicalCondition): bool
    {
        return $patientMedicalCondition->delete();
    }
}