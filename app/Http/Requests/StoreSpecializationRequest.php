<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:specializations,name|max:255',
            'description' => 'string|nullable'
        ];
    }

    
    public function messages(): array
    {
        return [
            'name.required' => 'The specialization name is required and cannot be empty.',
            'name.string'   => 'The specialization name must be a valid text string.',
            'name.unique'   => 'This medical specialization already exists in our records.',
            'name.max'      => 'The specialization name may not be greater than 255 characters.',

            'description.string' => 'description must be a valid string'
        ];
    }
}
