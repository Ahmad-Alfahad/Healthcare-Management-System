<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePharmacistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pharmacistId = $this->route('pharmacist'); 

        return [
            'facility_id'         => 'required|exists:facilities,id',
            'profile_id'          => [
                'required',
                'exists:profiles,id',
                Rule::unique('pharmacists', 'profile_id')->ignore($pharmacistId, 'id'),
            ],
            'degree'              => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:60',
            'license_number'      => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('pharmacists', 'license_number')->ignore($pharmacistId, 'id'),
            ],
            'is_active'           => 'required|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'profile_id.unique'      => 'This profile is already assigned to a pharmacist.',
            'license_number.unique'  => 'This pharmacy license number is already registered.',
        ];
    }
}
