<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabStaff extends Model
{
    protected $fillable = [
        "facility_id",
        "profile_id",
        "specialization"
    ];
}
