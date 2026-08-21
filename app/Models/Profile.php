<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'national_number',
        'phone',
        'gender',
        'address',
        'date_of_birth'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }
    public function pharmacist(): HasOne
    {
        return $this->hasOne(Pharmacist::class);
    }
    public function labStaff(): HasOne
    {
        return $this->hasOne(LabStaff::class);
    }

    // public function belongsToUser(User $user): bool
    // {
    //     return $this->user_id === $user->id;
    // }
}
