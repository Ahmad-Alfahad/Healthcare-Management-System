<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'         => 'required|integer|exists:users,id|unique:profiles,user_id',
            'full_name'       => 'required|string|max:255',
            'national_number' => 'nullable|string|max:20|unique:profiles,national_number',
            'phone'           => 'nullable|string|max:20',
            'gender'          => 'nullable|in:male,female',
            'date_of_birth'   => 'nullable|date|before:today',
            'address'         => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'            => 'The user ID is required.',
            'user_id.integer'             => 'The user ID must be an integer.',
            'user_id.exists'              => 'The selected user does not exist.',
            'user_id.unique'              => 'This user already has a profile.',
           
            'full_name.required'          => 'The full name is required.',
            'full_name.string'            => 'The full name must be a string.',
            'full_name.max'               => 'The full name must not exceed 255 characters.',
          
            'national_number.string'      => 'The national number must be a string.',
            'national_number.max'         => 'The national number must not exceed 20 characters.',
            'national_number.unique'      => 'The national number has already been taken.',
           
            'phone.string'                => 'The phone number must be a string.',
            'phone.max'                   => 'The phone number must not exceed 20 characters.',
           
            'gender.in'                   => 'The gender must be male or female.',
           
            'date_of_birth.date'          => 'The date of birth must be a valid date.',
            'date_of_birth.before'        => 'The date of birth must be before today.',
           
            'address.string'              => 'The address must be a string.',
            'address.max'                 => 'The address must not exceed 500 characters.',
        ];
    }
}
