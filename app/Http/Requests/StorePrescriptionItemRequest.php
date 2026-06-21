<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionItemRequest extends FormRequest
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
                'prescription_id' => ['required', 'integer', 'exists:prescriptions,id'],
                'medication_name' => ['required', 'string', 'max:255'],
                'dosage' => ['required', 'string', 'max:255'],
                'quantity_prescribed' => ['required', 'integer', 'min:1'],
                'frequency' => ['required', 'string', 'max:255'],
                'duration' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'prescription_id.required' => 'The prescription ID is required.',
            'prescription_id.integer' => 'The prescription ID must be an integer.',
            'prescription_id.exists' => 'The specified prescription does not exist.',

            'medication_name.required' => 'The medication name is required.',
            'medication_name.string' => 'The medication name must be a string.',
            'medication_name.max' => 'The medication name may not be greater than 255 characters.',

            'dosage.required' => 'The dosage is required.',
            'dosage.string' => 'The dosage must be a string.',
            'dosage.max' => 'The dosage may not be greater than 255 characters.',

            'quantity_prescribed.required' => 'The quantity prescribed is required.',
            'quantity_prescribed.integer' => 'The quantity prescribed must be an integer.',
            'quantity_prescribed.min' => 'The quantity prescribed must be at least 1.',

            'frequency.required' => 'The frequency is required.',
            'frequency.string' => 'The frequency must be a string.',
            'frequency.max' => 'The frequency may not be greater than 255 characters.',
            
            'duration.required' => 'The duration is required.',
            'duration.string' => 'The duration must be a string.',
            'duration.max' => 'The duration may not be greater than 255 characters.',
        ];
    }
}
