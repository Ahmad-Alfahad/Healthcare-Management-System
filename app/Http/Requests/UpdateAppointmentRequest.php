<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
                [
                   'nullable',
                    'integer',
                    'exists:patients,id'
                ],

            'doctor_id' => [
               'nullable',
                'integer',
                'exists:doctors,id'
            ],

            'status' => [
                'nullable',
                'in:pending,confirmed,cancelled,completed',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],

            'scheduled_date' => [
               'nullable',
                'date'
            ],

            'start_time' => [
                'nullable',
                'date_format:H:i'
            ],
            
        ];
    }

    public function message()
    {
        return [
            'patient_id.exists' => 'Selected patient does not exist.',

            'doctor_id.exists' => 'Selected doctor does not exist.',

            'reason.string' => ' reason must be a valid text string',
            'reason.max' => 'The reason  may not be greater than 500 characters.',

            'scheduled_date.date' => 'scheduled_date must be a valid date.',
            
            'start_time.date_format' => 'start_time must be a valid time.',

            'status.in' => 'Invalid appointment status.',
        ];
    }
}
