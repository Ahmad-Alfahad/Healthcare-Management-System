<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePharmacistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id'         => 'required|exists:employees,id|unique:pharmacists,employee_id',
            'degree'              => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:60',
            'license_number'      => 'nullable|string|unique:pharmacists,license_number|max:100',
            'is_active'           => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required'   => 'The pharmacist employee link is required.',
            'employee_id.exists'     => 'The selected employee does not exist in our records.',
            'employee_id.unique'     => 'This employee is already assigned to a pharmacist.',
            'license_number.unique'  => 'This pharmacy license number is already registered.',
            'is_active.boolean' => 'The active status must be true or false.',

            'years_of_experience.required' => 'The years of experience is required.',
            'years_of_experience.integer'  => 'The years of experience must be a valid integer.',
            'years_of_experience.min'      => 'The years of experience must be at least 0.',
            'years_of_experience.max'      => 'The years of experience must not exceed 60.',
        ];
    }
}
