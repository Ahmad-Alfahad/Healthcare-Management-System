<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'         => 'required|exists:users,id|unique:profiles,user_id',
            'full_name'       => 'required|string|max:255',
            'national_number' => 'nullable|string|unique:profiles,national_number',
            'phone'           => 'nullable|string',
            'gender'          => 'nullable|in:male,female',
            'date_of_birth'   => 'nullable|date',
            'address'         => 'nullable|string',
            // حقول إضافية مشروطة بناءً على نوع الحساب المرسل
            'specialization_id' => 'required_if:role,doctor|exists:specializations,id',
            'facility_id'     => 'required_if:role,doctor,pharmacist,laboratorian|exists:facilities,id',
            'blood_type'      => 'required_if:role,patient|string',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'The full name field is strictly required.',
            'user_id.unique'     => 'This user already has an active profile.',
            'specialization_id.required_if' => 'Specialization is required for doctor profiles.',
        ];
    }
}
