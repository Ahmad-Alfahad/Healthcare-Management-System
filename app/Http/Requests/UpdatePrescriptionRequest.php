<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionRequest extends FormRequest
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
            "visit_id" => [ 'sometimes', 'exists:visits,id'] ,
            "status" => ['sometimes', 'in:cancelled,pending,partial,dispensed'] ,
            "notes" => ['nullable', 'string'] ,
        ];
    }

    public function messages(): array
    {
        return [
            'visit_id.exists' => 'The specified visit does not exist.',

            'status.in' => 'Status must be one of the following: cancelled, pending, partial, dispensed.',
            
            'notes.string' => 'Notes must be a string.',
        ];
    }
}
