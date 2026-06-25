<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facility_id' => [
                'required',
                'exists:facilities,id'
            ],
            'department_id' => [
                'required',
                'exists:departments,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'facility_id.required' => 'Facility ID is required.',
            'facility_id.exists' => 'The specified facility does not exist.',
            'department_id.required' => 'Department ID is required.',
            'department_id.exists' => 'The specified department does not exist.',
        ];
    }
}
