<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Pharmacist extends Model
{
    use Auditable;

    use HasFactory;

    protected $fillable = [
        'employee_id',
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

    public function dispensings(): HasMany
    {
        return $this->hasMany(Dispensing::class, 'pharmacist_id');
    }
}
