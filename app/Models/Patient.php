<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class Patient extends Model
{
    use Auditable;

    protected $fillable = [
        'profile_id',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function patientMedicalConditions()
    {
        return $this->hasMany(PatientMedicalCondition::class);
    }
}
