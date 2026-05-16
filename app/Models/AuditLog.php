<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        "user_id",
        "table_name",
        "action",
        "record_id",
        "old_value",
        "new_value"
    ];


}
