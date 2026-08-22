<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class DoctorSchedule extends Model
{
    use Auditable;

    protected $fillable = [
        "doctor_id",
        "day_of_week",
        "is_off",
        "start_time",
        "end_time",
        "avg_consultation_time"
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
