<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class LabResult extends Model
{
    use Auditable;

    protected $fillable = [
        "lab_request_item_id",
        "lab_staff_id",
        "notes",
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
