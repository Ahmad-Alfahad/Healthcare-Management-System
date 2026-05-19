<?php

namespace App\Repositories;

use App\Models\MedicalCondition;
use Illuminate\Database\Eloquent\Collection;

class MedicalConditionRepository
{
    public function all(): collection
    {
        return MedicalCondition::all();
    }

        public function find(int $id): MedicalCondition
    {
        return MedicalCondition::findOrFail($id);
    }
    
    public function create(array $data): MedicalCondition
     {

        return MedicalCondition::create($data);
     }

    public function update( int $id,array $data): bool 
    {
         $medical_condition = MedicalCondition::findOrFail($id);
        return $medical_condition->update($data);
    }

    public function delete(int $id): bool
    {
        $medical_condition = MedicalCondition::findOrFail($id);
        return $medical_condition->delete();
    }
}