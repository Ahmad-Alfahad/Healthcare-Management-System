<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facility_id' => [
                'sometimes',
                'exists:facilities,id'
            ],
            'department_id' => [
                'sometimes',
                'exists:departments,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'facility_id.exists' => 'The specified facility does not exist.',
            'department_id.exists' => 'The specified department does not exist.',
        ];
    }
}
