<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLabRequestItemRequest extends FormRequest
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
            "visit_id" => ["sometimes", "integer","exists:visits,id"],
            "lab_test_id" => ["sometimes", "integer",  "exists:lab_tests,id"],
            "requested_at" => ["sometimes", "date"],
            "notes" => ["nullable", "string" , "max:255"]
        ];
    }

    public function messages(): array
    {
        return [
            "visit_id.integer" => "Visit ID must be an integer.",
            "visit_id.exists" => "The specified visit does not exist.",
        
            "lab_test_id.integer" => "Lab Test ID must be an integer.",
            "lab_test_id.exists" => "The specified lab test does not exist.",
        
            "requested_at.date" => "Requested at must be a valid date.",
        
            "notes.string" => "Notes must be a string.",
            "notes.max" => "Notes may not be greater than 255 characters."
        ];
    }
}
