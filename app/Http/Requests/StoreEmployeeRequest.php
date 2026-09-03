<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:doctor,pharmacist,laboratory',
            'full_name' => 'required|string|max:255',
            'national_number' => 'nullable|string|max:20|unique:profiles,national_number',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
            'biography' => 'nullable|string|max:2000',
            'achievements' => 'nullable|string|max:2000',
            'facility_id' => 'required|integer|exists:facilities,id',
            'languages' => 'nullable|array',
            'languages.*' => 'string|max:100',
            'is_active' => 'sometimes|boolean',
            'facility_department_specialization_id' => 'required_if:role,doctor|nullable|integer|exists:facility_department_specialization,id',
            'qualification' => 'required_if:role,doctor|nullable|string|max:255',
            'specialization' => 'required_if:role,laboratory|nullable|string|max:255',
            'degree' => 'required_if:role,pharmacist,laboratory|nullable|string|max:255',
            'years_of_experience' => 'nullable|integer|min:0|max:60',
            'license_number' => 'nullable|string|max:100|unique:pharmacists,license_number|unique:lab_staff,license_number',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The user name is required.',
            'name.string' => 'The user name must be a valid string.',
            'name.max' => 'The user name must not exceed 255 characters.',
            'email.required' => 'The user email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role.required' => 'The employee role is required.',
            'role.in' => 'The employee role must be doctor, pharmacist, or laboratory.',
            'full_name.required' => 'The employee full name is required.',
            'full_name.string' => 'The full name must be a valid string.',
            'full_name.max' => 'The full name must not exceed 255 characters.',
            'national_number.unique' => 'The national number has already been taken.',
            'phone.string' => 'The phone number must be a valid string.',
            'phone.max' => 'The phone number must not exceed 20 characters.',
            'gender.in' => 'The gender must be male or female.',
            'address.string' => 'The address must be a valid string.',
            'address.max' => 'The address must not exceed 500 characters.',
            'date_of_birth.date' => 'The date of birth must be a valid date.',
            'date_of_birth.before' => 'The date of birth must be before today.',
            'biography.string' => 'The biography must be a valid string.',
            'biography.max' => 'The biography must not exceed 2000 characters.',
            'achievements.string' => 'The achievements must be a valid string.',
            'achievements.max' => 'The achievements must not exceed 2000 characters.',
            'facility_id.required' => 'The employee facility is required.',
            'facility_id.integer' => 'The facility ID must be an integer.',
            'facility_id.exists' => 'The selected facility does not exist.',
            'languages.array' => 'The languages must be a valid array.',
            'languages.*.string' => 'Each language must be a valid string.',
            'languages.*.max' => 'Each language must not exceed 100 characters.',
            'is_active.boolean' => 'The active status must be true or false.',
            'facility_department_specialization_id.required_if' => 'The doctor work assignment is required.',
            'facility_department_specialization_id.exists' => 'The selected work assignment does not exist.',
            'qualification.required_if' => 'The doctor qualification is required.',
            'specialization.required_if' => 'The laboratory specialization is required.',
            'degree.required_if' => 'The professional degree is required.',
            'years_of_experience.integer' => 'The years of experience must be an integer.',
            'years_of_experience.min' => 'The years of experience must be at least 0.',
            'years_of_experience.max' => 'The years of experience must not exceed 60.',
            'license_number.unique' => 'This license number is already registered.',
        ];
    }
}
