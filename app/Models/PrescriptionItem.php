<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id',
        'medication_name',
        'dosage',
        'quantity_prescribed',
        'frequency',
        'duration',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

}
