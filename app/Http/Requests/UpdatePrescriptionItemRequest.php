<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionItemRequest extends FormRequest
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
            'medication_name' => ['sometimes', 'string', 'max:255'],
            'dosage' => ['sometimes', 'string', 'max:255'],
            'quantity_prescribed' => ['sometimes', 'integer', 'min:1'],
            'frequency' => ['sometimes', 'string', 'max:255'],
            'duration' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [

            'medication_name.string' => 'The medication name must be a string.',
            'medication_name.max' => 'The medication name may not be greater than 255 characters.',

            'dosage.string' => 'The dosage must be a string.',
            'dosage.max' => 'The dosage may not be greater than 255 characters.',

            'quantity_prescribed.integer' => 'The quantity prescribed must be an integer.',
            'quantity_prescribed.min' => 'The quantity prescribed must be at least 1.',

            'frequency.string' => 'The frequency must be a string.',
            'frequency.max' => 'The frequency may not be greater than 255 characters.',
            
            'duration.string' => 'The duration must be a string.',
            'duration.max' => 'The duration may not be greater than 255 characters.',
        ];
    }
}
