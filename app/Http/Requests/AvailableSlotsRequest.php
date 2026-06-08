<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AvailableSlotsRequest extends FormRequest
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
            'doctor_id' => [
                'required',
                'exists:doctors,id'
            ],

            'date' => [
                'required',
                'date'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.required' => 'Doctor ID is required.',
            'doctor_id.exists' => 'The specified doctor does not exist.',
            
            'date.required' => 'Date is required.',
            'date.date' => 'The date must be a valid date format.'
        ];
    }
}
