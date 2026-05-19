<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // جلب معرف المنشأة الحالية لتفادي ربط المنشأة بنفسها كأب
        $facilityId = $this->route('facility');

        return [
            'parent_id'     => "nullable|exists:facilities,id|not_in:{$facilityId}",
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
            'parent_id.not_in' => 'A facility cannot be its own parent or branch sub-facility.',

            'name.required' => 'The facility name is required for updates.',
            'name.string'   => 'The facility name must be a valid text string.',
            'name.max'      => 'The facility name may not be greater than 255 characters.',

            'facility_type.required' => 'The facility type is required.',
            'facility_type.string'   => 'The facility type must be a valid text string.',
            'facility_type.max'      => 'The facility type may not be greater than 100 characters.',

            'phone_number.required' => 'The facility phone number is required.',
            'phone_number.string'   => 'The phone number format is invalid.',
            'phone_number.max'      => 'The phone number may not be greater than 20 characters.',

            'address.required' => 'The facility address is required.',
            'address.string'   => 'The address must be a valid text string.',
            'address.max'      => 'The address may not be greater than 500 characters.',
        ];
    }
}
