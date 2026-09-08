<?php

namespace App\Http\Requests;

use App\Models\PatientMedicalCondition;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientMedicalConditionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $condition = $this->route('patient_medical_condition');
        $condition = $condition instanceof PatientMedicalCondition
            ? $condition
            : PatientMedicalCondition::find($condition);

        return $condition !== null && $this->user()?->can('update', $condition);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
            'diagnosed_at' => ['sometimes', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'notes.string' => 'Notes must be a valid text string.',

            'diagnosed_at.date' => 'Diagnosed at must be a valid date.',
        ];
    }
}
