<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePharmacistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facility_id'         => 'required|exists:facilities,id',
            'profile_id'          => 'required|exists:profiles,id|unique:pharmacists,profile_id',
            'degree'              => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:60',
            'license_number'      => 'nullable|string|unique:pharmacists,license_number|max:100',
            'is_active'           => 'nullable|boolean'
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