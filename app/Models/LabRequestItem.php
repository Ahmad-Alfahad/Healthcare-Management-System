<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabRequestItem extends Model
{
    protected $fillable = [
        "visit_id",
        "lab_test_id",
        "requested_at",
        "notes"
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

}
