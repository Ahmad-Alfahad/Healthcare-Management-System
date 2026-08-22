<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class Visit extends Model
{
    use Auditable;

    protected $fillable = [
        "appointment_id",
        "doctor_id",
        "patient_id",
        "status",
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

    public function labRequestItems()
    {
        return $this->hasMany(LabRequestItem::class);
    }
}
