<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLab_testRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255' , 'unique:lab_tests,name'],
            'range_high' => ['required', 'numeric', 'gt:range_low'],
            'range_low' => ['required', 'numeric' , 'lt:range_high'],
            'unit' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.unique' => 'The name has already been taken.',
          
            'range_high.required' => 'The range high field is required.',
            'range_high.numeric' => 'The range high must be a number.',
            'range_high.gt' => 'The range high must be greater than range low.',
          
            'range_low.required' => 'The range low field is required.',
            'range_low.numeric' => 'The range low must be a number.',
            'range_low.lt' => 'The range low must be less than range high.',
          
            'unit.required' => 'The unit field is required.',
            'unit.string' => 'The unit must be a string.',
            'unit.max' => 'The unit may not be greater than 255 characters.',
        ];
    }
}
