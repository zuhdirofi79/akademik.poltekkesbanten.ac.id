<?php

namespace App\Http\Requests;

use App\Models\ServiceType;
use App\Support\ServiceFormConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmissionFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->route('slug')) {
            $slug = ServiceFormConfig::resolveSlug($this->route('slug'));
            $serviceType = ServiceType::where('slug', $slug)->first();

            if ($serviceType && ! $serviceType->is_active) {
                abort(410, 'Layanan ini tidak tersedia.');
            }
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('slug')) {
            $this->merge([
                'resolved_slug' => ServiceFormConfig::resolveSlug($this->route('slug')),
            ]);
        }
    }

    public function rules(): array
    {
        $slug = $this->input('resolved_slug', ServiceFormConfig::resolveSlug($this->route('slug')));
        $config = ServiceFormConfig::get($slug);

        abort_if(! $config, 404);

        $fileRule = ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
        $isActivityGroup = in_array($slug, ServiceFormConfig::activityGroupSlugs(), true);
        $isReactivation = $slug === ServiceFormConfig::reactivationSlug();

        $rules = [
            'nim' => ['required', 'string', 'max:50'],
            'full_name' => ['required', 'string', 'max:255'],
            'nik' => $isActivityGroup || $isReactivation
                ? ['nullable', 'digits:16']
                : ['required', 'digits:16'],
            'study_program_id' => ['required', 'exists:study_programs,id'],
            'email' => ['required', 'email', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'additional_note' => ['nullable', 'string', 'max:2000'],
            'data_confirmed' => ['accepted'],
            'website' => ['nullable', 'max:0'],
            'captcha_answer' => ['required', 'integer'],
            'captcha_expected' => ['required', 'integer'],
        ];

        if ($slug === 'perubahan-data-mahasiswa-aktif') {
            $rules['mother_name'] = ['required', 'string', 'max:255'];
            $rules['request_type'] = ['required', 'string', Rule::in($config['request_types'])];
            $rules['corrected_data'] = ['required', 'string', 'max:2000'];
            $rules['supporting_data'] = ['required', ...$fileRule];
        }

        if (in_array($slug, ServiceFormConfig::semesterSlugs(), true)) {
            $rules['semester'] = ['required', 'string', 'max:50'];
        }

        if ($slug === 'surat-keterangan-aktif-kuliah') {
            $rules['active_krs'] = ['required', ...$fileRule];
            $rules['purpose'] = ['required', 'string', 'max:2000'];
        }

        if ($slug === ServiceFormConfig::berkelakuanBaikSlug()) {
            $rules['active_krs'] = ['required', ...$fileRule];
            $rules['purpose'] = ['required', 'string', 'max:2000'];
        }

        if ($slug === ServiceFormConfig::rekomendasiSlug()) {
            $rules['alumni_supporting_data'] = ['required', ...$fileRule];
            $rules['active_krs'] = ['required', ...$fileRule];
            $rules['purpose'] = ['required', 'string', 'max:2000'];
        }

        if ($slug === ServiceFormConfig::beasiswaSlug()) {
            $rules['gpa_score'] = ['required', 'numeric', 'min:0', 'max:4'];
            $rules['active_krs'] = ['required', ...$fileRule];
            $rules['purpose'] = ['required', 'string', 'max:2000'];
        }

        if ($slug === 'surat-keterangan-registrasi') {
            $rules['student_registration_proof'] = ['required', ...$fileRule];
            $rules['purpose'] = ['required', 'string', 'max:2000'];
        }

        if ($slug === 'surat-cuti-mahasiswa') {
            $rules['leave_registration_proof'] = ['required', ...$fileRule];
            $rules['leave_request_letter'] = ['required', ...$fileRule];
            $rules['statement_letter'] = ['required', ...$fileRule];
            $rules['leave_reason'] = ['required', 'string', 'max:2000'];
        }

        if ($slug === 'surat-keluar-mahasiswa') {
            $rules['khs_active_proof'] = ['required', ...$fileRule];
            $rules['statement_letter'] = ['required', ...$fileRule];
            $rules['exit_reason'] = ['required', 'string', 'max:2000'];
        }

        if ($slug === 'surat-keterangan-verifikasi-ijazah-alumni') {
            $rules['entry_year'] = ['required', 'string', 'max:10'];
            $rules['graduation_year'] = ['required', 'string', 'max:10'];
            $rules['certificate_number'] = ['nullable', 'string', 'max:100'];
            $rules['purpose'] = ['required', 'string', 'max:2000'];
            $rules['diploma'] = ['required', ...$fileRule];
            $rules['additional_supporting_data'] = ['nullable', ...$fileRule];
        }

        if ($slug === 'siakad') {
            $rules['issue_type'] = ['required', 'string', Rule::in($config['issue_types'])];
            $rules['optional_supporting_data'] = ['nullable', ...$fileRule];
        }

        if ($isActivityGroup) {
            $rules['activity_name'] = ['required', 'string', 'max:255'];
            $rules['activity_date'] = ['nullable', 'date'];
            $rules['activity_location'] = ['nullable', 'string', 'max:255'];
            $rules['participant_list'] = ['required', 'string', 'max:10000'];
        }

        if ($slug === 'surat-tugas-mahasiswa') {
            $rules['proposal_or_invitation'] = ['required', ...$fileRule];
        }

        if ($slug === 'nota-dinas-dispen-kegiatan-mahasiswa') {
            $rules['invitation_or_proposal'] = ['required', ...$fileRule];
        }

        if ($isReactivation) {
            $rules['semester'] = ['nullable', 'string', 'max:50'];
            $rules['previous_status'] = ['required', 'string', Rule::in($config['previous_status_options'] ?? ['Cuti', 'Keluar'])];
            $rules['reactivation_reason'] = ['nullable', 'string', 'max:2000'];
            $rules['reactivation_registration_proof'] = ['required', ...$fileRule];
            $rules['reactivation_statement_letter'] = ['required', ...$fileRule];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('website')) {
                $validator->errors()->add('website', 'Spam terdeteksi.');
            }

            if ((int) $this->input('captcha_answer') !== (int) $this->input('captcha_expected')) {
                $validator->errors()->add('captcha_answer', 'Jawaban captcha tidak sesuai.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'data_confirmed.accepted' => 'Anda harus menyetujui bahwa data yang diisi benar.',
            'nik.digits' => 'NIK harus berupa 16 digit angka.',
            'supporting_data.required' => 'File data dukung wajib diunggah.',
            'alumni_supporting_data.required' => 'DATA DUKUNG ALUMNI - SURAT PERNYATAAN MAGANG DAN SCAN IJAZAH wajib diunggah.',
            'active_krs.required' => 'Data Dukung - KRS Periode Aktif wajib diunggah.',
            'student_registration_proof.required' => 'File bukti registrasi mahasiswa wajib diunggah.',
            'leave_registration_proof.required' => 'File bukti registrasi cuti wajib diunggah.',
            'leave_request_letter.required' => 'File surat permohonan cuti wajib diunggah.',
            'statement_letter.required' => 'Data Dukung - Surat Pernyataan Tertulis Bermaterai wajib diunggah.',
            'khs_active_proof.required' => 'Data Dukung - Bukti KHS Aktif wajib diunggah.',
            'gpa_score.required' => 'IPS/IPK wajib diisi.',
            'gpa_score.numeric' => 'IPS/IPK harus berupa angka.',
            'gpa_score.max' => 'IPS/IPK maksimal 4.00.',
            'diploma.required' => 'File ijazah wajib diunggah.',
            'proposal_or_invitation.required' => 'Data Dukung - Proposal atau Surat Undangan wajib diunggah.',
            'invitation_or_proposal.required' => 'Data Dukung - Bukti Undangan / Proposal wajib diunggah.',
            'reactivation_registration_proof.required' => 'Data Dukung - Bukti Registrasi Cuti ke Akademik Jurusan wajib diunggah.',
            'reactivation_statement_letter.required' => 'Data Dukung - Surat Pernyataan Bermaterai Aktif Kembali wajib diunggah.',
            'participant_list.required' => 'Daftar mahasiswa yang mengikuti kegiatan wajib diisi.',
            'activity_name.required' => 'Nama kegiatan wajib diisi.',
            'previous_status.required' => 'Status sebelumnya wajib dipilih.',
            '*.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
            '*.max' => 'Ukuran file maksimal 5 MB.',
        ];
    }
}
