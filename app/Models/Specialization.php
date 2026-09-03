<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class Specialization extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'description',
    ];

    public function facilityDepartmentSpecializations()
    {
        return $this->hasMany(FacilityDepartmentSpecialization::class);
    }
}
