<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            'profile_id' => [
                'sometimes',
                'integer',
                'exists:profiles,id',
                Rule::unique('employees', 'profile_id')->ignore($employeeId, 'id'),
            ],
            'facility_id' => 'sometimes|integer|exists:facilities,id',
            'languages' => 'nullable|array',
            'languages.*' => 'string|max:100',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'profile_id.integer' => 'The profile ID must be an integer.',
            'profile_id.exists' => 'The selected profile does not exist.',
            'profile_id.unique' => 'This profile is already assigned to another employee.',
            'facility_id.integer' => 'The facility ID must be an integer.',
            'facility_id.exists' => 'The selected facility does not exist.',
            'languages.array' => 'The languages must be a valid array.',
            'languages.*.string' => 'Each language must be a valid string.',
            'languages.*.max' => 'Each language must not exceed 100 characters.',
            'is_active.boolean' => 'The active status must be true or false.',
        ];
    }
}
