<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
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
            "visit_id" => [ 'required', 'exists:visits,id'] ,
            "status" => ['required', 'in:cancelled,pending,partial,dispensed'] ,
            "notes" => ['nullable', 'string'] ,
        ];
    }

    public function messages(): array
    {
        return [
            'visit_id.required' => 'Visit ID is required.',
            'visit_id.exists' => 'The specified visit does not exist.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of the following: cancelled, pending, partial, dispensed.',
            
            'notes.string' => 'Notes must be a string.',
        ];
    }
}
