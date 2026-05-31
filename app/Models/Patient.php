<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
protected $fillable = [
    'profile_id', 'blood_type', 'height', 'weight', 
    'allergies', 'chronic_diseases', 'medical_history', 
    'emergency_contact_name', 'emergency_contact_phone', 
    'emergency_contact_relation', 'insurance_provider', 'insurance_number'
];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class) ;
    }

    public function visits()
    {
        return $this->hasMany(Visit::class) ;
    }

}

