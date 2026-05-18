<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalConditionRequest extends FormRequest
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
            'name' => ['required' , 'string' , 'max:255' , 'unique:medical_conditions,name'],
            'type' => ['required' , 'in:allergy,chronic'],
            'notes' => ['nullable' , 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Medical condition name is required.',
            'name.string' => 'The Medical condition name must be a valid text string.',
            'name.max' => 'The Medical condition name may not be greater than 255 characters.',
            'name.unique' => 'This medical condition already exists.',

            'type.required' => 'Medical condition type is required.',
            'type.in' => 'Invalid medical condition type.',

            'notes.string' => 'note must be a valid text string.'
        ];

    }
}
