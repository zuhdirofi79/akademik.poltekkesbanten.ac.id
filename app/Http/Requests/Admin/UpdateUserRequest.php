<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::in(['admin', 'superadmin', 'admin_jurusan'])],
            'study_program_id' => [
                Rule::requiredIf(fn () => $this->input('role') === 'admin_jurusan'),
                'nullable',
                'integer',
                'exists:study_programs,id',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->route('user');
            $newRole = $this->input('role');

            if ($user->id === $this->user()->id && $newRole !== 'superadmin') {
                $validator->errors()->add('role', 'Anda tidak dapat mengubah role superadmin milik sendiri.');
            }

            if ($user->isSuperAdmin() && $newRole !== 'superadmin') {
                $activeSuperadmins = \App\Models\User::where('role', 'superadmin')
                    ->where('is_active', true)
                    ->where('id', '!=', $user->id)
                    ->count();

                if ($activeSuperadmins < 1) {
                    $validator->errors()->add('role', 'Minimal harus ada satu superadmin aktif.');
                }
            }
        });
    }
}
