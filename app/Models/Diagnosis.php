<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class Diagnosis extends Model
{
    use Auditable;

    protected $fillable = [
        "visit_id",
        "diagnosis_code",
        "description",
        "diagnosis_type",
        "notes"
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
