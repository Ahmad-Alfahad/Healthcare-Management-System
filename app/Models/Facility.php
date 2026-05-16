<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = [
        "parent_id",
        "name",
        "facility_type",
        "phone_number",
        "address"
    ];


}
