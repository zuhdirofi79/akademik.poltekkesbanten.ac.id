<?php

namespace App\Http\Requests;

use App\Models\AcademicSatisfactionSurvey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSatisfactionSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $level = $this->input('satisfaction_level');
        $allowedReasons = AcademicSatisfactionSurvey::reasonsByLevel()[$level] ?? [];

        return [
            'satisfaction_level' => ['required', Rule::in(['puas', 'cukup_puas', 'kurang_puas'])],
            'service_type_id' => ['nullable', 'exists:service_types,id'],
            'reason' => [
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($allowedReasons): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (! in_array($value, $allowedReasons, true)) {
                        $fail('Alasan tidak sesuai dengan tingkat kepuasan yang dipilih.');
                    }
                },
            ],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'respondent_email' => ['nullable', 'email', 'max:255'],
            'ticket_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'satisfaction_level.required' => 'Silakan pilih tingkat kepuasan.',
        ];
    }
}
