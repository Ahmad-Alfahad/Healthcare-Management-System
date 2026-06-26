<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
       return [
        'facility_department_specialization_id' => 'required|exists:facility_department_specialization,id',
        'profile_id'            => 'required|exists:profiles,id|unique:doctors,profile_id',
        'qualification'         => 'required|string|max:255',
        'years_of_experience'   => 'required|integer|min:0',
        'biography'             => 'nullable|string|max:2000',
        'achievements'          => 'nullable|string|max:2000',
        'languages'             => 'nullable|array',
    ];
    }

    public function messages(): array
    {
        return [
            'facility_department_specialization_id.required' => 'The facility, department, and specialization assignment is required.',
            'facility_department_specialization_id.exists'   => 'The selected work configuration combination does not exist.',
            'profile_id.required' => 'The doctor profile link is required.',
            'profile_id.exists'   => 'The selected profile does not exist in our records.',
            'profile_id.unique'   => 'This profile is already assigned to an existing doctor account.',
            'qualification.required' => 'The doctor qualification is required.',
            'qualification.string'   => 'The doctor qualification must be a valid string.',
            'qualification.max'      => 'The doctor qualification must not exceed 255 characters.',
            'years_of_experience.required' => 'The years of experience is required.',
            'years_of_experience.integer'  => 'The years of experience must be a valid integer.',
            'years_of_experience.min'      => 'The years of experience must be at least 0.',
            'biography.string' => 'The biography must be a valid string.',
            'biography.max'    => 'The biography must not exceed 2000 characters.',
            'achievements.string' => 'The achievements must be a valid string.',    
            'achievements.max'    => 'The achievements must not exceed 2000 characters.',
            'languages.array' => 'The languages must be a valid array.',
                
        ];
    }
}
