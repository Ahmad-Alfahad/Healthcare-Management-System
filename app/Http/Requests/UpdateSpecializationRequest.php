<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $specializationId = $this->route('specialization');

        return [
            'name' => "string|max:255|unique:specializations,name,{$specializationId}",
            'description' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'   => 'The specialization name must be a valid text string.',
            'name.unique'   => 'This medical specialization name is already assigned to another record.',
            'name.max'      => 'The specialization name may not be greater than 255 characters.',

            'description.string'=>'description must be a valid string'
        ];
    }

}
