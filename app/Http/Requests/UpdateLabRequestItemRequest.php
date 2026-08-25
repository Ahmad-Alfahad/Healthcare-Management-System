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
            "notes" => ["nullable", "string", "max:255"]
        ];
    }

    public function messages(): array
    {
        return [
            "notes.string" => "Notes must be a string.",
            "notes.max" => "Notes may not be greater than 255 characters."
        ];
    }
}
