<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 
                ['required' , 
                'integer' , 
                'exists:patients,id'
                ],

            'doctor_id' => [
                'required' ,
                'integer' ,
                'exists:doctors,id'
                 ] ,

            'status' => [
                'in:pending,confirmed,cancelled,completed'
                 ] ,

            'reason' => [
                'nullable' ,
                'string' ,
                'max:500',
            ] , 

            'scheduled_date' => [
                'required' ,
                'after_or_equal:today',
                'date'
            ] ,

            'start_time' => [
                'required' ,
                'date'
            ],
        ];
    }

    public function message() 
    {
          return [

            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',

            'doctor_id.required' => 'Doctor is required.',
            'doctor_id.exists' => 'Selected doctor does not exist.',
            
            'reason.string' => ' reason must be a valid text string' ,
            'reason.max' => 'The reason  may not be greater than 500 characters.' ,

            'scheduled_date.required' =>  'scheduled_date  is required.',
            'scheduled_date.after_or_equal' => 'scheduled_date  cannot be in the past.',

            'start_time.required' => 'start_time  is required.',

            'status.in' =>
                'Invalid appointment status.',
        ];
    }
}
