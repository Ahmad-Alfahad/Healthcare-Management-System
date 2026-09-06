<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->route('profile');

        return [
            'user_id'           => [
                'sometimes',
                'integer',
                'exists:users,id',
                Rule::unique('profiles', 'user_id')->ignore($profileId),
            ],
            'full_name'         => 'sometimes|string|max:255|regex:/^[\pL\s]+$/u',
            'national_number'   => [
                'nullable',
                'string',
                'numeric',
                'digits_between:8,15',
                'max:20',
                Rule::unique('profiles', 'national_number')->ignore($profileId),
            ],
            'phone'             => 'nullable|string|max:20|numeric|digits_between:8,15',
            'gender'            => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date|before:today',
            'address'       => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.integer'               => 'The user ID must be an integer.',
            'user_id.exists'                => 'The selected user does not exist.',
            'user_id.unique'                => 'This user already has a profile.',

            'full_name.string'              => 'The full name must be a string.',
            'full_name.max'                 => 'The full name must not exceed 255 characters.',
           'full_name.regex'               => 'The full name may only contain letters and spaces.',

            'national_number.string'        => 'The national number must be a string.',
            'national_number.max'           => 'The national number must not exceed 20 characters.',
            'national_number.unique'        => 'The national number has already been taken.',
            'national_number.numeric'       => 'The national number must be a number.',
            'national_number.digits_between' => 'The national number must be between 8 and 15 digits.',

            'phone.string'                  => 'The phone number must be a string.',
            'phone.max'                     => 'The phone number must not exceed 20 characters.',
            'phone.numeric'                 => 'The phone number must be a number.',
            'phone.digits_between'          => 'The phone number must be between 8 and15 digits.',
           
            'gender.in'                     => 'The gender must be male or female.',
           
            'date_of_birth.date'            => 'The date of birth must be a valid date.',
            'date_of_birth.before'          => 'The date of birth must be before today.',
           
            'address.string'                => 'The address must be a string.',
            'address.max'                   => 'The address must not exceed 500 characters.',
        ];
    }
}
