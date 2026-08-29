<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDispensingRequest extends FormRequest
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
            'prescription_item_id' => ['required','integer' ,'exists:prescription_items,id'],
            'quantity_dispensed' => ['required', 'integer', 'min:1'],
            // These values are security-sensitive and must always be set by the server.
            'pharmacist_id' => ['prohibited'],
            'dispensed_at' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'prescription_item_id.required' => 'The prescription item ID is required.',
            'prescription_item_id.exists' => 'The selected prescription item does not exist.',
            'prescription_item_id.integer' => 'The prescription item ID must be an integer.',
            
            'pharmacist_id.prohibited' => 'The pharmacist is determined from the authenticated user.',

            'quantity_dispensed.required' => 'The quantity dispensed is required.',
            'quantity_dispensed.integer' => 'The quantity dispensed must be an integer.',
            'quantity_dispensed.min' => 'The quantity dispensed must be at least 1.',
           
            'dispensed_at.prohibited' => 'The dispensing date is determined by the server.',
        ];
    }
}
