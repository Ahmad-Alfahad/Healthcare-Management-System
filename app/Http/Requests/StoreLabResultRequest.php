<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLabResultRequest extends FormRequest
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
            "lab_request_item_id" => ["required", "integer", "exists:lab_request_items,id"],
            "value" => ["required", "numeric", "between:0,9999.99"],
            "access_token" => ["nullable", "string", "max:255"],
            "notes" => ["nullable", "string"],
        ];
    }

    public function messages(): array
    {
        return [
            "lab_request_item_id.required" => "Lab request item ID is required.",
            "lab_request_item_id.integer" => "Lab request item ID must be an integer.",
            "lab_request_item_id.exists" => "The specified lab request item does not exist.",

            "lab_staff_id.required" => "Lab staff ID is required.",
            "lab_staff_id.integer" => "Lab staff ID must be an integer.",
            "lab_staff_id.exists" => "The specified lab staff does not exist.",

            "value.required" => "Value is required.",
            "value.numeric" => "Value must be a numeric value.",
            "value.between" => "Value must be between 0 and 9999.99.",
            
            "access_token.string" => "Access token must be a string.",
            "access_token.max" => "Access token may not be greater than 255 characters.",
          
            "notes.string" => "Notes must be a string.",
        ];
    }
}
