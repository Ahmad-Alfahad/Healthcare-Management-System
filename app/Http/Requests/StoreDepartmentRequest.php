<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|unique:departments,name|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The department name is required and cannot be empty.',
            'name.string'   => 'The department name must be a valid text string.',
            'name.unique'   => 'This department name already exists in the medical system.',
            'name.max'      => 'The department name may not be greater than 255 characters.',
            'description.string' => 'The description must be a valid text.',
            'description.max'    => 'The description may not exceed 1000 characters.',
        ];
    }
}
