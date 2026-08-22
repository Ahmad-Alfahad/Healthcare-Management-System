<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class MedicalCondition extends Model
{
    use Auditable;

    //

    protected $fillable = [
        'name',
        'type',
        'notes',
    ];

    public function patientMedicalConditions()
    {
        return $this->hasMany(
            PatientMedicalCondition::class
        );
    }
}
