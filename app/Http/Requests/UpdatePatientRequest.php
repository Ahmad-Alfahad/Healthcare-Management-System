<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $patient = $this->route('patient');
        $profileId = $patient ? $patient->profile_id : null;

        return [
            'full_name' => 'sometimes|required|string|max:255',
            'national_number' => ['nullable', 'string', Rule::unique('profiles', 'national_number')->ignore($profileId)],
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
}