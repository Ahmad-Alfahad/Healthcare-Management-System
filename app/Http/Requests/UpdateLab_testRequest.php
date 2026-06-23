<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLab_testRequest extends FormRequest
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
        $lab_test = $this->route('lab_test');
        return [
            'name' => ['sometimes', 'string', 'max:255' , Rule::unique('lab_tests')->ignore($lab_test)],
            'range_high' => ['sometimes', 'numeric'],
            'range_low' => ['sometimes', 'numeric'],
            'unit' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.unique' => 'The name has already been taken.',

            'range_high.numeric' => 'The range high must be a number.',
          
            'range_low.numeric' => 'The range low must be a number.',
          
            'unit.string' => 'The unit must be a string.',
            'unit.max' => 'The unit may not be greater than 255 characters.',
        ];
    }
}
