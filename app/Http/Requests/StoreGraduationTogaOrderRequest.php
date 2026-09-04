<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreGraduationTogaOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'toga_size' => 'All Size',
        ]);
    }

    public function rules(): array
    {
        return [
            'student_name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:30'],
            'study_program_id' => ['required', 'exists:study_programs,id'],
            'graduation_year' => ['required', 'integer', 'digits:4', 'min:2000', 'max:2099'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'toga_size' => ['required', 'string', Rule::in(['All Size'])],
            'size_note' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'confirm' => ['accepted'],
            'captcha_answer' => ['required', 'integer'],
            'captcha_expected' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirm.accepted' => 'Anda harus menyetujui pernyataan kebenaran data pemesanan.',
            'study_program_id.required' => 'Program studi wajib dipilih.',
            'graduation_year.required' => 'Tahun lulus wajib diisi.',
            'graduation_year.digits' => 'Tahun lulus harus 4 digit.',
            'phone.required' => 'Nomor HP/Telepon wajib diisi.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ((int) $this->input('captcha_answer') !== (int) $this->input('captcha_expected')) {
                $validator->errors()->add('captcha_answer', 'Jawaban captcha tidak sesuai.');
            }
        });
    }
}
