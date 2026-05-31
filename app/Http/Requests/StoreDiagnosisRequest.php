<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
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
            "visit_id" => ["required", "integer", "exists:visits,id"],
            "diagnosis_code" => ["required", "string", "max:50"],
            "description" => ["required", "string"],
            "diagnosis_type" => ["required", "string", "max:255"],
            "notes" => ["nullable", "string"]
        ];
    }

    public function messages(): array
    {
        return [
            "visit_id.required" => "The visit ID is required.",
            "visit_id.integer" => "The visit ID must be an integer.",
            "visit_id.exists" => "The specified visit does not exist.",

            "diagnosis_code.required" => "The diagnosis code is required.",
            "diagnosis_code.string" => "The diagnosis code must be a string.",
            "diagnosis_code.max" => "The diagnosis code may not be greater than 50 characters.",

            "description.required" => "The description is required.",
            "description.string" => "The description must be a string.",

            "diagnosis_type.required" => "The diagnosis type is required.",
            "diagnosis_type.string" => "The diagnosis type must be a string.",
            "diagnosis_type.max" => "The diagnosis type may not be greater than 255 characters.",

            "notes.string" => "The notes must be a string."
        ];
    }
}
