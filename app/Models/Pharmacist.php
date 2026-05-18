<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pharmacist extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'profile_id',
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
}