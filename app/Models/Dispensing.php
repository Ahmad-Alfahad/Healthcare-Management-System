<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensing extends Model
{
    protected $fillable = [
        "prescription_item_id",
        "pharmacist_id",
        "quantity_dispensed",
        "dispensed_at"
    ];

    public function prescriptionItem()
    {
        return $this->belongsTo(PrescriptionItem::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(Pharmacist::class);
    }
}



