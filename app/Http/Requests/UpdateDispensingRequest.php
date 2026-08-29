<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDispensingRequest extends FormRequest
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
            'prescription_item_id' => ['sometimes', 'integer', 'exists:prescription_items,id'],
            'quantity_dispensed' => ['sometimes', 'integer', 'min:1'],
            // Preserve the original audit trail even when an administrator corrects a record.
            'pharmacist_id' => ['prohibited'],
            'dispensed_at' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'prescription_item_id.exists' => 'The selected prescription item does not exist.',
            'prescription_item_id.integer' => 'The prescription item ID must be an integer.',

            'pharmacist_id.prohibited' => 'The pharmacist cannot be changed after dispensing.',

            'quantity_dispensed.integer' => 'The quantity dispensed must be an integer.',
            'quantity_dispensed.min' => 'The quantity dispensed must be at least 1.',
            
            'dispensed_at.prohibited' => 'The dispensing date cannot be changed after dispensing.',
        ];
    }
}
