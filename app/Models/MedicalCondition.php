<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalCondition extends Model
{
    //

    protected $fillable = [
     'name' ,
     'type' ,
     'notes',
    ] ;

    public function patientMedicalConditions()
{
    return $this->hasMany(
        PatientMedicalCondition::class
    );
}

}
