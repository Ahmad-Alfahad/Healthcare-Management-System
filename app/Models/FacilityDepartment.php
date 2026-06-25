<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityDepartment extends Model
{
    protected $table = 'facility_department';

    protected $fillable = ['facility_id', 'department_id'];


    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function facilityDepartmentSpecializations()
    {
        return $this->hasMany(
            FacilityDepartmentSpecialization::class
        );
    }
}
