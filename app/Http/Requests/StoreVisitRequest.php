<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
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
            "appointment_id" => [
                "required",
                "exists:appointments,id"  ] ,
                
            "doctor_id" => [
                "nullable",
                "exists:doctors,id"  ] ,

            "patient_id" => [
                "nullable",
                "exists:patients,id"  ] ,

            "notes" => ["string" ] ,

            "visited_at" => [
                "required",
                "date" ]

        ];
    }

    public function messages(): array
    {
        return [
            "appointment_id.required" => "The appointment ID is required.",
            "appointment_id.exists" => "The specified appointment does not exist.",

            "doctor_id.exists" => "The specified doctor does not exist.",
            
            "patient_id.exists" => "The specified patient does not exist.",

            "notes.string" => "Notes must be a string.",
            
            "visited_at.required" => "The visit date and time are required.",
            "visited_at.date" => "The visit date and time must be a valid date."
        ];
    }
}
