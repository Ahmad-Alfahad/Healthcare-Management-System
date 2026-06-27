<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => [
                'sometimes',
                'exists:profiles,id',
                Rule::unique('patients', 'profile_id')->ignore($this->route('patient')),
            ],

            'blood_type' => [
                'nullable',
                Rule::in([
                    'A+',
                    'A-',
                    'B+',
                    'B-',
                    'AB+',
                    'AB-',
                    'O+',
                    'O-',
                ]),
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'emergency_contact_relation' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'profile_id.exists' => 'The selected profile does not exist.',
            'profile_id.unique' => 'This profile is already associated with another patient.',

            'blood_type.in' => 'The selected blood type is invalid. Accepted types are: A+, A-, B+, B-, AB+, AB-, O+, O-.',

            'emergency_contact_name.string' => 'The emergency contact name must be a string.',
            'emergency_contact_name.max' => 'The emergency contact name must not exceed 255 characters.',

            'emergency_contact_phone.string' => 'The emergency contact phone must be a string.',
            'emergency_contact_phone.max' => 'The emergency contact phone must not exceed 20 characters.',

            'emergency_contact_relation.string' => 'The emergency contact relation must be a string.',
            'emergency_contact_relation.max' => 'The emergency contact relation must not exceed 100 characters.',
        ];
    }
}
