<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id'         => 'required|exists:employees,id|unique:lab_staff,employee_id',
            'specialization'      => 'sometimes|string|max:255',
            'degree'              => 'sometimes|string|max:255|regex:/^[\pL\s]+$/u',
            'years_of_experience' => 'sometimes|integer|min:0|max:60|numeric|digits_between:1,2',
            'license_number'      => 'nullable|string|unique:lab_staff,license_number|max:100',
            'is_active'           => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required'    => 'The lab staff employee link is required.',
            'employee_id.exists'      => 'The selected employee does not exist in our records.',
            'employee_id.unique'      => 'This employee is already assigned to a lab staff member.',

            'degree.regex' => 'The degree may only contain letters and spaces.',
            'years_of_experience.numeric' => 'The years of experience must be a number.',
            'years_of_experience.digits_between' => 'The years of experience must be between 1 and 2 digits.',
        ];
    }
}
