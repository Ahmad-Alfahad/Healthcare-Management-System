<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
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
