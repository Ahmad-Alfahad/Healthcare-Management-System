<?php

namespace App\Http\Requests;

use App\Models\Visit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $visit = $this->route('visit');
        $visit = $visit instanceof Visit ? $visit : Visit::find($visit);

        return $visit !== null && $this->user()?->can('update', $visit);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_id' => [
                'sometimes',
                'integer',
                'exists:appointments,id',
            ],

            'notes' => ['sometimes', 'string'],

        ];
    }

    public function messages(): array
    {
        return [
            'appointment_id.exists' => 'The specified appointment does not exist.',

            'notes.string' => 'Notes must be a string.',
        ];
    }
}
