<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientMedicalConditionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $record = $this->route(
            'patient_medical_condition'
        );

        return [
            'patient_id' => ['sometimes', 'exists:patients,id'],
            'medical_condition_id' => [
                'sometimes',
                'exists:medical_conditions,id'
                ,
                Rule::unique('patient_medical_conditions')
                    ->where(
                        fn($query) =>
                        $query->where(
                            'patient_id',
                            $this->patient_id
                        )
                    )
                    ->ignore($record),
            ],
            'notes' => ['nullable', 'string'],
            'diagnosed_at' => ['sometimes', 'date']
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.exists' => 'The specified patient does not exist.',

            'medical_condition_id.exists' => 'The specified medical condition does not exist.',

            'notes.string' => 'Notes must be a valid text string.',

            'diagnosed_at.date' => 'Diagnosed at must be a valid date.'
        ];
    }
}