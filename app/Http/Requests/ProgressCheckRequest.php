<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgressCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'progress_token' => ['required', 'string', 'max:64'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'NIM atau email wajib diisi.',
            'progress_token.required' => 'Nomor tiket atau token wajib diisi.',
        ];
    }
}
