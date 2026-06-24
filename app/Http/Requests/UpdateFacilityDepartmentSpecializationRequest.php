<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityDepartmentSpecializationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'facility_department_id' => [
                'sometimes',
                'exists:facility_department,id'
            ],

            'specialization_id' => [
                'sometimes',
                'exists:specializations,id'
            ],

            'is_active' => ['sometimes' , 'boolean']

        ];
    }

    public function messages()
    {
        return [
            'facility_department_id.exists' => 'The specified facility_department does not exist.',
            
            'specialization_id.exists' => 'The specified specialization does not exist.',
        ];
    }
}
