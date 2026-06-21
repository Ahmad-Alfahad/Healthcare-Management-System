<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDiagnosisRequest extends FormRequest
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
            "diagnosis_code" => ["sometimes", "string", "max:50"],
            "description" => ["sometimes", "string"],
            "diagnosis_type" => ["sometimes", "string", "max:255"],
            "notes" => ["sometimes", "nullable", "string"]
        ];
    }

    public function messages(): array
    {
        return [
           
            "diagnosis_code.string" => "The diagnosis code must be a string.",
            "diagnosis_code.max" => "The diagnosis code may not be greater than 50 characters.",

            "description.string" => "The description must be a string.",
            "diagnosis_type.string" => "The diagnosis type must be a string.",

            "diagnosis_type.max" => "The diagnosis type may not be greater than 255 characters.",
            
            "notes.string" => "The notes must be a string."
        ];
    }
}
