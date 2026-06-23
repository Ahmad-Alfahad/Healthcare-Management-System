<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('department');

        return [
            'name'        => "sometimes|string|max:255|unique:departments,name,{$departmentId}",
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'sometimes|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The department name is required for adjustment.',
            'name.string'   => 'The department name must be a valid text string.',
            'name.unique'   => 'This department name is already taken by another department.',
            'name.max'      => 'The department name may not be greater than 255 characters.',
        ];
    }
}
