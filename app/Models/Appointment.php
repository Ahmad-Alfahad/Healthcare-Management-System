<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class Appointment extends Model
{
    use Auditable;

    protected $fillable = [
        "patient_id",
        "doctor_id",
        "status",
        "reason",
        "scheduled_date",
        "start_time",
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function visit()
    {
        return $this->hasOne(Visit::class);
    }
}
