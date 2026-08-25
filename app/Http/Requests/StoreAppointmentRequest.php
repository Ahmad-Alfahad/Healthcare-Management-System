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

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if ($user?->isPatient()) {
            $this->merge([
                'patient_id' => $user->patient?->id,
            ]);
        }

        if ($user?->isDoctor()) {
            $this->merge([
                'doctor_id' => $user->doctor?->id,
            ]);
        }
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
                'required',
                'integer',
                'exists:patients,id'
            ],

            'doctor_id' => [
                'required',
                'integer',
                'exists:doctors,id'
            ],

            'status' => [
                'in:pending,confirmed,cancelled,completed'
            ],

            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],

            'scheduled_date' => [
                'required',
                'date'
            ],

            'start_time' => [
                'required',
                'date_format:H:i'
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'patient_id.required' => 'Patient is required.',
            'patient_id.exists' => 'Selected patient does not exist.',

            'doctor_id.required' => 'Doctor is required.',
            'doctor_id.exists' => 'Selected doctor does not exist.',

            'reason.string' => ' reason must be a valid text string',
            'reason.max' => 'The reason  may not be greater than 500 characters.',

            'scheduled_date.required' => 'scheduled_date  is required.',
            'scheduled_date.date' => 'scheduled_date must be a valid date.',

            'status.in' => 'Invalid appointment status.',

            'start_time.required' => 'start_time  is required.',
            'start_time.date_format' => 'start_time must be a valid time.',

        ];
    }
}
