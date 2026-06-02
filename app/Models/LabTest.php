<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    protected $fillable = [
        "name",
        "range_high",
        "range_low",
        "unit"
    ];

    public function labRequestItems()
    {
        return $this->hasMany(LabRequestItem::class);
    }
}
