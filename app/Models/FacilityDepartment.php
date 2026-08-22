<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class FacilityDepartment extends Model
{
    use Auditable;

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

    public function facilityDepartmentSpecializations(): HasMany
    {
        return $this->hasMany(
            FacilityDepartmentSpecialization::class,
            'facility_department_id'
        );
    }
}
