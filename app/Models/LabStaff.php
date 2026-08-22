<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class LabStaff extends Model
{
    use Auditable;

    use HasFactory;

    protected $table = 'lab_staff';

    protected $fillable = [
        'facility_id',
        'profile_id',
        'specialization',
        'degree',
        'years_of_experience',
        'license_number',
        'is_active'
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
        'is_active'           => 'boolean',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class, 'lab_staff_id');
    }
}
