<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class LabTest extends Model
{
    use Auditable;

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
