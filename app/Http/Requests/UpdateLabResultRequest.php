<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLabResultRequest extends FormRequest
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
            "status" => ["sometimes", "in:pending,processing,completed,cancelled"],
            "value" => ["sometimes", "numeric", "between:0,9999.99"],
            "access_token" => ["nullable", "string", "max:255"],
            "notes" => ["nullable", "string"]
        ];
    }

    public function messages(): array
    {
        return [
            "status.required" => "Status is required.",
            "status.in" => "Status must be one of the following: pending, processing, completed, cancelled.",

            "value.required" => "Value is required.",
            "value.numeric" => "Value must be a numeric value.",
            "value.between" => "Value must be between 0 and 9999.99.",

            "access_token.string" => "Access token must be a string.",
            "access_token.max" => "Access token may not be greater than 255 characters.",
           
            "notes.string" => "Notes must be a string."
        ];
    }
}
