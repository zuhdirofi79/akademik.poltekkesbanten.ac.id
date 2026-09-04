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

    public function rules(): array
    {
        return [
            'student_name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:30'],
            'study_program_id' => ['required', 'exists:study_programs,id'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'toga_size' => ['required', Rule::in(['S', 'M', 'L', 'XL', 'XXL', 'Custom'])],
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
            'phone.required' => 'Nomor HP/Telepon wajib diisi.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ((int) $this->input('captcha_answer') !== (int) $this->input('captcha_expected')) {
                $validator->errors()->add('captcha_answer', 'Jawaban captcha tidak sesuai.');
            }

            if ($this->input('toga_size') === 'Custom' && ! filled($this->input('size_note'))) {
                $validator->errors()->add('size_note', 'Catatan ukuran wajib diisi jika memilih ukuran Custom.');
            }
        });
    }
}
