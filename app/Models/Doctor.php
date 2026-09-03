<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Doctor extends Model
{
    use Auditable;

    use HasFactory;

    protected $fillable = [
        'facility_department_specialization_id',
        'employee_id',
        'qualification',
        'years_of_experience',
        'biography',
        'achievements',
    ];
    protected $casts = [
        'years_of_experience' => 'integer',
    ];

    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->employee?->is_active;
    }

    public function facilityDepartmentSpecialization(): BelongsTo
    {
        return $this->belongsTo(
            FacilityDepartmentSpecialization::class,
            'facility_department_specialization_id'
        );
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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function doctorSchedule(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
