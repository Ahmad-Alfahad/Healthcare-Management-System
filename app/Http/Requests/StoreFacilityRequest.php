<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id'     => 'nullable|exists:facilities,id',
            'name'          => 'required|string|max:255',
            'facility_type' => 'required|string|max:100',
            'phone_number'  => 'required|string|max:20',
            'address'       => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => 'The selected parent facility does not exist in our records.',

            'name.required' => 'The facility name is required and cannot be left empty.',
            'name.string'   => 'The facility name must be a valid text string.',
            'name.max'      => 'The facility name may not be greater than 255 characters.',

            'facility_type.required' => 'Please specify the facility type (e.g., Hospital, Clinic).',
            'facility_type.string'   => 'The facility type must be a valid text string.',
            'facility_type.max'      => 'The facility type may not be greater than 100 characters.',

            'phone_number.required' => 'The facility phone number is required.',
            'phone_number.string'   => 'The phone number format is invalid.',
            'phone_number.max'      => 'The phone number may not be greater than 20 characters.',

            'address.required' => 'The physical address of the facility is required.',
            'address.string'   => 'The address must be a valid text string.',
            'address.max'      => 'The address may not be greater than 500 characters.',
        ];
    }
}
