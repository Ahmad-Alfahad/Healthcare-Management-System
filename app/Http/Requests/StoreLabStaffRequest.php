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
            'specialization'      => 'required|string|max:255',
            'degree'              => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:60',
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
            'specialization.required' => 'The lab technical specialization field is required.',
            'degree.required'         => 'The academic degree field is required.',
        ];
    }
}
