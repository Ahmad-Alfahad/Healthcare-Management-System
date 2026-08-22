<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Facility extends Model
{
    use Auditable;

    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'facility_type',
        'phone_number',
        'address'
    ];
    protected $casts = [
        'parent_id' => 'integer',
    ];
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'parent_id');
    }


    public function childrens(): HasMany
    {
        return $this->hasMany(Facility::class, 'parent_id');
    }

    public function facilityDepartments(): HasMany
    {
        return $this->hasMany(FacilityDepartment::class, 'facility_id');
    }
}
