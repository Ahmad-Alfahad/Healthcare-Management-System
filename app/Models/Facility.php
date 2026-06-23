<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'facility_type',
        'phone_number',
        'address'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'parent_id');
    }


    public function children(): HasMany
    {
        return $this->hasMany(Facility::class, 'parent_id');
    }

    public function facilityDepartment(): HasMany
    {
        return $this->hasMany(FacilityDepartment::class);
    }
}
