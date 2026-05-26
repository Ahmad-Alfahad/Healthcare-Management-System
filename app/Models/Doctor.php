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
        'languages'
    ];
    protected $casts = [
        'years_of_experience' => 'integer',
    ];

    public function workConfiguration(): BelongsTo
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
}
