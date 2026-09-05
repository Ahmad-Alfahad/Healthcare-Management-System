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

    protected $appends = ['remaining_quantity'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function dispensings()
    {
        return $this->hasMany(Dispensing::class);
    }

    public function getRemainingQuantityAttribute(): int
    {
        $prescribedQuantity = (int) $this->quantity_prescribed;
        $dispensedQuantity = (int) ($this->dispensings_sum_quantity_dispensed ?? $this->dispensings()->sum('quantity_dispensed'));

        return max(0, $prescribedQuantity - $dispensedQuantity);
    }
}
