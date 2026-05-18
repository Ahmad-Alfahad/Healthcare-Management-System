<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facility_id'         => 'required|exists:facilities,id',
            'profile_id'          => 'required|exists:profiles,id|unique:lab_staff,profile_id',
            'specialization'      => 'required|string|max:255',
            'degree'              => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:60',
            'license_number'      => 'nullable|string|unique:lab_staff,license_number|max:100',
            'is_active'           => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'facility_id.required'    => 'The assigned medical facility is required.',
            'profile_id.unique'       => 'This profile is already assigned to a lab staff member.',
            'specialization.required' => 'The lab technical specialization field is required.',
            'degree.required'         => 'The academic degree field is required.',
        ];
    }
}
