<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'superadmin', 'admin_jurusan'])],
            'study_program_id' => [
                Rule::requiredIf(fn () => $this->input('role') === 'admin_jurusan'),
                'nullable',
                'integer',
                'exists:study_programs,id',
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
