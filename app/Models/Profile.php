<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Concerns\Auditable;
use App\Models\User;

class Profile extends Model
{
    use Auditable, HasFactory;

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
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }   
}
