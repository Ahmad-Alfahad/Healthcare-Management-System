<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctorId = $this->route('doctor');

        return [
            'facility_department_specialization_id' => 'sometimes|exists:facility_department_specialization,id',
            'employee_id' => [
                'sometimes',
                'exists:employees,id',
                Rule::unique('doctors', 'employee_id')->ignore($doctorId, 'id'),
            ],
            'qualification'         => 'sometimes|string|max:255',
            'years_of_experience'   => 'sometimes|integer|min:0',
            'biography'             => 'nullable|string|max:2000',
            'achievements'          => 'nullable|string|max:2000',
            'languages'             => 'nullable|array',
            'is_active'             => 'sometimes|boolean',
        ];
      
    }

    public function messages(): array
    {
        return [
            'facility_department_specialization_id.required' => 'The work assignment configuration is required for updating.',
            'employee_id.required' => 'The doctor employee is required for updating.',
            'employee_id.exists'   => 'The selected employee does not exist in our records.',
            'employee_id.unique'   => 'This employee is already assigned to another doctor account.',
            'qualification.required' => 'The doctor qualification is required for updating.',
            'qualification.string'   => 'The doctor qualification must be a valid string.',
            'qualification.max'      => 'The doctor qualification must not exceed 255 characters.',
            'years_of_experience.required' => 'The years of experience is required for updating.',
            'years_of_experience.integer'  => 'The years of experience must be a valid integer.',
            'years_of_experience.min'      => 'The years of experience must be at least 0.',
            'biography.string' => 'The biography must be a valid string.',
            'biography.max'    => 'The biography must not exceed 2000 characters.',
            'achievements.string' => 'The achievements must be a valid string.',
            'achievements.max'    => 'The achievements must not exceed 2000 characters.',
            'languages.array' => 'The languages must be a valid array.',
            'is_active.boolean' => 'The active status must be true or false.',
            
        ];
    }
}
