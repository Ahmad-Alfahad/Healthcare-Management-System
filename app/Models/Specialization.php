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
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function facilityDepartmentSpecializations()
    {
        return $this->hasMany(FacilityDepartmentSpecialization::class);
    }
}
