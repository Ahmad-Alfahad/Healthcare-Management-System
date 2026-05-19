<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
      
        return [
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'national_number' => 'nullable|string|unique:profiles,national_number',
            'phone' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'blood_type' => 'nullable|string|max:5',
            'height' => 'nullable|numeric|between:30,250',
            'weight' => 'nullable|numeric|between:2,500',
            'allergies' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user account is invalid.',
            'full_name.required' => 'The patient full name field is mandatory.',
            'national_number.unique' => 'This national number has already been registered.',
            'gender.in' => 'Gender must be either male or female.',
            'height.numeric' => 'Height must be a valid numerical value.',
        ];
    }
}
