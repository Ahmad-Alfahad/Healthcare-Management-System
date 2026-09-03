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
        'employee_id',
        'specialization',
        'degree',
        'years_of_experience',
        'license_number',
    ];

    protected $casts = [
        'years_of_experience' => 'integer',
    ];

    public function getIsActiveAttribute(): bool
    {
        return $this->employee?->is_active ?? true;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getProfileAttribute(): ?Profile
    {
        return $this->employee?->profile;
    }

    public function getFacilityAttribute(): ?Facility
    {
        return $this->employee?->facility;
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class, 'lab_staff_id');
    }
}
