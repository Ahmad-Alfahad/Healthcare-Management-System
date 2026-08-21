<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityDepartmentSpecialization extends Model
{
    protected $table = 'facility_department_specialization';

    protected $fillable = ['facility_department_id', 'specialization_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function facilityDepartment(): BelongsTo
    {
        return $this->belongsTo(FacilityDepartment::class, 'facility_department_id');
    }


    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class
        , 'facility_department_specialization_id');
    }
}
