<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    protected $fillable = [
        "lab_request_item_id",
        "lab_staff_id",
        "notes",
        "status",
        "value",
        "unit",
        "reference_range",
        "access_token",
        "completed_at"
    ];

    public function labRequestItem()
    {
        return $this->belongsTo(LabRequestItem::class);
    }

    public function labStaff()
    {
        return $this->belongsTo(LabStaff::class);
    }
}
