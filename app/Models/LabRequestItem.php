<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabRequestItem extends Model
{
    protected $fillable = [
        "visit_id",
        "lab_tast_id",
        "requested_at",
        "notes"
    ];
}
