<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        "appointment_id",
        "doctor_id",
        "patient_id",
        "notes",
        "visited_at"
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

}
