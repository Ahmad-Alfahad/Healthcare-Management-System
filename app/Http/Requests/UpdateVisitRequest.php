<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
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
                "sometimes",
                "exists:appointments,id"  ] ,
                
            "doctor_id" => [
                "sometimes",
                "exists:doctors,id"  ] ,

            "patient_id" => [
                "sometimes",
                "exists:patients,id"  ] ,

            "notes" => ["string" ] ,

            "visited_at" => [
                "sometimes",
                "date" ]
        ];
    }

    public function messages(): array
    {
        return [
            "appointment_id.exists" => "The specified appointment does not exist.",

            "doctor_id.exists" => "The specified doctor does not exist.",
            
            "patient_id.exists" => "The specified patient does not exist.",

            "notes.string" => "Notes must be a string.",
            
            "visited_at.date" => "The visit date and time must be a valid date."
        ];
    }
}
