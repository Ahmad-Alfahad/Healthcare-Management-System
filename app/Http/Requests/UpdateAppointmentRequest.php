<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');
        $appointment = $appointment instanceof Appointment ? $appointment : Appointment::find($appointment);

        return $appointment !== null && $this->user()?->can('update', $appointment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'patient_id' => [
                'sometimes',
                'integer',
                'exists:patients,id',
            ],

            'doctor_id' => [
                'sometimes',
                'integer',
                'exists:doctors,id',
            ],

            'status' => [
                'sometimes',
                'in:pending,confirmed,cancelled,completed',
            ],

            'reason' => [
                'sometimes',
                'string',
                'max:500',
            ],

            'scheduled_date' => [
                'sometimes',
                'date',
            ],

            'start_time' => [
                'sometimes',
                'date_format:H:i',
            ],

        ];
    }

    public function messages()
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
