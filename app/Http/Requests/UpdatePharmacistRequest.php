<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePharmacistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pharmacistId = $this->route('pharmacist'); 

        return [
            'employee_id'         => [
                'sometimes',
                'exists:employees,id',
                Rule::unique('pharmacists', 'employee_id')->ignore($pharmacistId, 'id'),
            ],
            'degree'              => 'sometimes|string|max:255|regex:/^[\pL\s]+$/u',
            'years_of_experience' => 'sometimes|integer|min:0|max:60|numeric|digits_between:1,2',
            'license_number'      => [
                'nullable',
                'string',
                'regex:/^[\pL\s]+$/u',
                'max:100',
                Rule::unique('pharmacists', 'license_number')->ignore($pharmacistId, 'id'),
            ],
            'is_active'           => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required'   => 'The pharmacist employee is required for updating.',
            'employee_id.exists'     => 'The selected employee does not exist in our records.',
            'employee_id.unique'     => 'This employee is already assigned to another pharmacist.',
            'license_number.unique'  => 'This pharmacy license number is already registered.',
            'degree.regex'           => 'The degree may only contain letters and spaces.',
            'years_of_experience.numeric' => 'The years of experience must be a number.',
            'years_of_experience.digits_between' => 'The years of experience must be between 1 and 2 digits.',
        ];
    }
}
