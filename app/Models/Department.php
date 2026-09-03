<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class Department extends Model
{
    use Auditable;

    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function facilityDepartment()
    {
        return $this->hasMany(FacilityDepartment::class);
    }
}
