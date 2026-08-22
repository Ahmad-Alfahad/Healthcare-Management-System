<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class PrescriptionItem extends Model
{
    use Auditable;

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

    public function dispensings()
    {
        return $this->hasMany(Dispensing::class);
    }
}
