<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_department_specialization_id',
        'profile_id',
        'qualification',
        'years_of_experience',
        'biography',
        'achievements',
        'languages',
        'is_active'
    ];
    protected $casts = [
        'years_of_experience' => 'integer',
        'languages' => 'array',
        'is_active' => 'boolean',
    ];

    public function facilityDepartmentSpecialization(): BelongsTo
    {
        return $this->belongsTo(FacilityDepartmentSpecialization::class, 'facility_department_specialization_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function appointments() 
    {
        return $this->hasMany(Appointment::class) ;
    }

    public function doctorSchedule() 
    {
        return $this->hasMany(DoctorSchedule::class) ;
    }

    public function visits()
    {
        return $this->hasMany(Visit::class) ;
    }
}
