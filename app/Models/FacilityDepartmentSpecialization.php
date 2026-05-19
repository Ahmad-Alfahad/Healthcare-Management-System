<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityDepartmentSpecialization extends Model
{
    protected $table = 'facility_department_specialization';

    protected $fillable = ['facility_department_id', 'specialization_id'];

    public function facilityDepartment(): BelongsTo
    {
        return $this->belongsTo(FacilityDepartment::class, 'facility_department_id');
    }

    
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
