<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorScheduleRequest extends FormRequest
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
            'doctor_id' => [ 'required' , 'integer' ,'exists:doctors,id'],
            'day_of_week' => ['required','integer','between:0,6'],
            'is_off' => ['required','boolean'],
            'start_time' => ['required_if:is_off,false','date_format:H:i'],
            'end_time' => ['required_if:is_off,false','date_format:H:i','after:start_time'],
            'avg_consultation_time' => ['required_if:is_off,false','integer','min:1']
        ];
    }

    public function messages() 
    {
        return [
            'doctor_id.required' => 'Doctor ID is required.',
            'doctor_id.integer' => 'Doctor ID must be an integer.',
            'doctor_id.exists' => 'The specified doctor does not exist.',

            'day_of_week.required' => 'Day of the week is required.',
            'day_of_week.integer' => 'Day of the week must be an integer.',
            'day_of_week.between' => 'Day of the week must be between 0 (Sunday) and 6 (Saturday).',
            
            'is_off.required' => 'Off day status is required.',
            'is_off.boolean' => 'Off day status must be true or false.',

            'start_time.required_if' => 'Start time is required when the doctor is not off.',
            'start_time.date_format' => 'Start time must be in the format HH:MM.',

            'end_time.required_if' => 'End time is required when the doctor is not off.',
            'end_time.date_format' => 'End time must be in the format HH:MM.',
            'end_time.after' => 'End time must be after start time.',
            
            'avg_consultation_time.required_if' => 'Average consultation time is required when the doctor is not off.',
            'avg_consultation_time.integer' => 'Average consultation time must be an integer.',
            'avg_consultation_time.min' => 'Average consultation time must be at least 1 minute.'
        ];
    }
}
