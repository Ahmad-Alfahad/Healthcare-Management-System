<?php

namespace App\Http\Requests;

use App\Models\LabStaff;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateLabStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $labstaff = $this->route('labstaff');

        $labStaffId = $labstaff instanceof LabStaff ? $labstaff->id : $labstaff;


        return [
            'facility_id' => 'required|exists:facilities,id',

            'profile_id'  => [
                'required',
                'exists:profiles,id',
                Rule::unique('lab_staff', 'profile_id')->ignore($labStaffId, 'id'),
            ],

            'specialization'      => 'required|string|max:255',
            'degree'              => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:60',

            'license_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('lab_staff', 'license_number')->ignore($labStaffId, 'id'),
            ],

            'is_active' => 'required|boolean'
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
