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
            'name'          => 'sometimes|string|max:255',
            'facility_type' => 'sometimes|string|max:100|in:pharmacy,laboratory,clinic,hospital',
            'phone_number'  => 'sometimes|string|max:20',
            'address'       => 'sometimes|string|max:500',
        ];
    }
    public function messages(): array
    {
        return [
            'parent_id.exists' => 'The selected parent facility does not exist in our records.',
            'parent_id.not_in' => 'A facility cannot be its own parent or branch sub-facility.',

            'name.string'   => 'The facility name must be a valid text string.',
            'name.max'      => 'The facility name may not be greater than 255 characters.',

            'facility_type.string'   => 'The facility type must be a valid text string.',
            'facility_type.max'      => 'The facility type may not be greater than 100 characters.',
            'facility_type.in'      => 'The facility type must be valid Type.',

            'phone_number.string'   => 'The phone number format is invalid.',
            'phone_number.max'      => 'The phone number may not be greater than 20 characters.',

            'address.string'   => 'The address must be a valid text string.',
            'address.max'      => 'The address may not be greater than 500 characters.',
        ];
    }
}
