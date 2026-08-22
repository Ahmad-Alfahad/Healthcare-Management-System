<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Auditable;

class PatientMedicalCondition extends Model
{
    use Auditable;

    //
    use HasFactory;

    protected $fillable = [

        'patient_id',

        'medical_condition_id',

        'diagnosed_at',

        'notes',
    ];

    protected $casts = [

        'diagnosed_at' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(
            Patient::class
        );
    }

    public function medicalCondition()
    {
        return $this->belongsTo(
            MedicalCondition::class
        );
    }
}
