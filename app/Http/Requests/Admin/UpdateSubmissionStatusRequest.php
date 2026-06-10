<?php

namespace App\Http\Requests\Admin;

use App\Enums\SubmissionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubmissionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(SubmissionStatus::values())],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
