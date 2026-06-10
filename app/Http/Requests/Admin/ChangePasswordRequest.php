<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $rules = [
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['required'],
        ];

        if (! $this->user()->must_change_password) {
            $rules['current_password'] = ['required'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->user()->must_change_password && ! Hash::check($this->input('current_password'), $this->user()->password)) {
                $validator->errors()->add('current_password', 'Password saat ini tidak sesuai.');
            }
        });
    }
}
