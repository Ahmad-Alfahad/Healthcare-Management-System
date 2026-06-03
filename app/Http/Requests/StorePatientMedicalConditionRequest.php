<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientMedicalConditionRequest extends FormRequest
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
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'medical_condition_id' => ['required', 'exists:medical_conditions,id' 
            , Rule::unique('patient_medical_conditions')->where(function ($query) {
                return $query->where('patient_id', $this->input('patient_id'))
                    ->where('medical_condition_id', $this->input('medical_condition_id'));
            })
            ],
            
            'notes' => ['nullable', 'string'],
            'diagnosed_at' => ['required', 'date']
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient ID is required.',
            'patient_id.exists' => 'The specified patient does not exist.',
           
            'medical_condition_id.required' => 'Medical condition ID is required.',
            'medical_condition_id.exists' => 'The specified medical condition does not exist.',
             'medical_condition_id.unique' => 'This medical condition is already associated with the patient.',

            'notes.string' => 'Notes must be a valid text string.',
           
            'diagnosed_at.date' => 'Diagnosed at must be a valid date.'
        ];
    }
}