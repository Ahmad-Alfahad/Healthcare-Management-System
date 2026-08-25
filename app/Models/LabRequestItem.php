<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class LabRequestItem extends Model
{
    use Auditable;

    protected $fillable = [
        "visit_id",
        "lab_test_id",
        "requested_at",
        "notes",
        "status"
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    public function labResult()
    {
        return $this->hasOne(LabResult::class);
    }
}
