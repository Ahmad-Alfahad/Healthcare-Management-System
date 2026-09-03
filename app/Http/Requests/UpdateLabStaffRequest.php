<?php

namespace App\Http\Requests;

use App\Models\LabStaff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateLabStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $labstaff = $this->route('labstaff');

        $labStaffId = $labstaff instanceof LabStaff ? $labstaff->id : $labstaff;


        return [
            'employee_id'  => [
                'sometimes',
                'exists:employees,id',
                Rule::unique('lab_staff', 'employee_id')->ignore($labStaffId, 'id'),
            ],

            'specialization'      => 'sometimes|string|max:255',
            'degree'              => 'sometimes|string|max:255',
            'years_of_experience' => 'sometimes|integer|min:0|max:60',

            'license_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('lab_staff', 'license_number')->ignore($labStaffId, 'id'),
            ],

            'is_active' => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required'    => 'The lab staff employee is required for updating.',
            'employee_id.exists'      => 'The selected employee does not exist in our records.',
            'employee_id.unique'      => 'This employee is already assigned to another lab staff member.',
            'specialization.required' => 'The lab technical specialization field is required.',
            'degree.required'         => 'The academic degree field is required.',
        ];
    }
}
