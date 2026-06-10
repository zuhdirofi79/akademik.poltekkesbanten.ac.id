@extends('layouts.app')

@section('title', $config['title'] . ' - Layanan Akademik')

@section('content')
@php
    $slug = $config['slug'];
    $semesterSlugs = \App\Support\ServiceFormConfig::semesterSlugs();
    $aktifKuliahSlugs = \App\Support\ServiceFormConfig::aktifKuliahPatternSlugs();
@endphp

<div class="page-title-bar">{{ $config['title'] }}</div>

<section class="container form-layout">
    <div class="form-card" style="grid-column: 1 / -1;">
        <div class="form-card-header">📄 {{ $config['card_title'] }}</div>
        <div class="form-card-body">
            <aside class="form-sidebar">
                <h3>{{ $config['info_title'] }}</h3>
                <p>{{ $config['info_text'] }}</p>

                <div class="progress-box">
                    <strong>Progress Layanan:</strong>
                    <p style="margin:0.5rem 0 0;">{{ $serviceType->name }} {{ $stats['year'] }}</p>
                    <p style="margin:0.25rem 0;">Sudah Diproses: <strong>{{ $stats['processed'] }}</strong></p>
                    <p style="margin:0.25rem 0;">Belum Diproses: <strong>{{ $stats['pending'] }}</strong></p>
                </div>

                <p style="margin-top:1.5rem;font-size:0.9rem;color:#6b7280;">
                    Direktorat Poltekkes Kemenkes Banten - Administrasi dan Akademik
                </p>
            </aside>

            <div class="form-main">
                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin:0;padding-left:1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/layanan/' . $config['slug']) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nim">NIM</label>
                            <input type="text" name="nim" id="nim" class="form-control" value="{{ old('nim') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="nik">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" maxlength="16" required>
                        </div>

                        <div class="form-group full">
                            <label for="full_name">
                                @if($slug === 'surat-keterangan-verifikasi-ijazah-alumni')
                                    Nama Lengkap Alumni
                                @else
                                    Nama Lengkap
                                @endif
                            </label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="study_program_id">Program Studi</label>
                            <select name="study_program_id" id="study_program_id" class="form-select" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($studyPrograms as $program)
                                    <option value="{{ $program->id }}" @selected(old('study_program_id') == $program->id)>{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                @if($slug === 'surat-keterangan-verifikasi-ijazah-alumni')
                                    Email Aktif
                                @elseif($slug === 'surat-rekomendasi')
                                    Email Mahasiswa/Alumni
                                @else
                                    Email Mahasiswa
                                @endif
                            </label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Masukan Email Aktif" required>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" required>
                        </div>

                        @if($slug === 'perubahan-data-mahasiswa-aktif')
                            <div class="form-group full">
                                <label for="mother_name">Nama Ibu Kandung</label>
                                <input type="text" name="mother_name" id="mother_name" class="form-control" value="{{ old('mother_name') }}" required>
                            </div>
                            <div class="form-group full">
                                <label for="request_type">Jenis Permohonan Perbaikan Data</label>
                                <select name="request_type" id="request_type" class="form-select" required>
                                    <option value="">-- Pilih Jenis Permohonan --</option>
                                    @foreach($config['request_types'] as $type)
                                        <option value="{{ $type }}" @selected(old('request_type') == $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group full">
                                <label for="corrected_data">Tuliskan data yang benar yang akan diubah</label>
                                <textarea name="corrected_data" id="corrected_data" class="form-control" rows="3" required>{{ old('corrected_data') }}</textarea>
                            </div>
                            <div class="form-group full">
                                <label for="supporting_data">Data Dukung Kesesuaian Data (PDF/JPG/PNG)</label>
                                <input type="file" name="supporting_data" id="supporting_data" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                        @endif

                        @if(in_array($slug, $semesterSlugs))
                            <div class="form-group">
                                <label for="semester">Semester</label>
                                <input type="text" name="semester" id="semester" class="form-control" value="{{ old('semester') }}" required>
                            </div>
                        @endif

                        @if(in_array($slug, $aktifKuliahSlugs))
                            <div class="form-group full">
                                <label for="registration_proof">Data Dukung - Bukti Registrasi</label>
                                <input type="file" name="registration_proof" id="registration_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="active_krs">Data Dukung - KRS Periode Aktif</label>
                                <input type="file" name="active_krs" id="active_krs" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="purpose">{{ $config['purpose_label'] ?? 'Keperluan Surat' }}</label>
                                <textarea name="purpose" id="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
                            </div>
                        @endif

                        @if($slug === 'surat-keterangan-registrasi')
                            <div class="form-group full">
                                <label for="student_registration_proof">Data Dukung - Bukti Registrasi Mahasiswa</label>
                                <input type="file" name="student_registration_proof" id="student_registration_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="purpose">Keperluan Surat</label>
                                <textarea name="purpose" id="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
                            </div>
                        @endif

                        @if($slug === 'surat-cuti-mahasiswa')
                            <div class="form-group full">
                                <label for="leave_registration_proof">Data Dukung - Bukti Registrasi Cuti</label>
                                <input type="file" name="leave_registration_proof" id="leave_registration_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="leave_request_letter">Data Dukung - Surat Permohonan Cuti</label>
                                <input type="file" name="leave_request_letter" id="leave_request_letter" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="leave_reason">Alasan Cuti</label>
                                <textarea name="leave_reason" id="leave_reason" class="form-control" rows="3" required>{{ old('leave_reason') }}</textarea>
                            </div>
                        @endif

                        @if($slug === 'surat-keterangan-verifikasi-ijazah-alumni')
                            <div class="form-group">
                                <label for="entry_year">Tahun Masuk</label>
                                <input type="text" name="entry_year" id="entry_year" class="form-control" value="{{ old('entry_year') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="graduation_year">Tahun Lulus</label>
                                <input type="text" name="graduation_year" id="graduation_year" class="form-control" value="{{ old('graduation_year') }}" required>
                            </div>
                            <div class="form-group full">
                                <label for="certificate_number">Nomor Ijazah (opsional)</label>
                                <input type="text" name="certificate_number" id="certificate_number" class="form-control" value="{{ old('certificate_number') }}">
                            </div>
                            <div class="form-group full">
                                <label for="purpose">Keperluan Verifikasi</label>
                                <textarea name="purpose" id="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
                            </div>
                            <div class="form-group full">
                                <label for="diploma">Data Dukung - Ijazah</label>
                                <input type="file" name="diploma" id="diploma" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="additional_supporting_data">Data Dukung Tambahan (opsional)</label>
                                <input type="file" name="additional_supporting_data" id="additional_supporting_data" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        @endif

                        @if($slug === 'siakad')
                            <div class="form-group full">
                                <label for="issue_type">Kendala SIAKAD</label>
                                <select name="issue_type" id="issue_type" class="form-select" required>
                                    <option value="">-- Pilih Kendala --</option>
                                    @foreach($config['issue_types'] as $type)
                                        <option value="{{ $type }}" @selected(old('issue_type') == $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group full">
                                <label for="optional_supporting_data">Data Dukung (opsional)</label>
                                <input type="file" name="optional_supporting_data" id="optional_supporting_data" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        @endif

                        <div class="form-group full">
                            <label for="additional_note">Keterangan Lainnya</label>
                            <textarea name="additional_note" id="additional_note" class="form-control" rows="3" placeholder="{{ $config['additional_note_placeholder'] ?? 'Opsional, isi (-) jika tidak ada' }}">{{ old('additional_note') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="captcha_answer">Captcha: {{ $captcha['question'] }}</label>
                            <input type="number" name="captcha_answer" id="captcha_answer" class="form-control" required>
                            <input type="hidden" name="captcha_expected" value="{{ $captcha['expected'] }}">
                        </div>

                        <div class="form-group honeypot">
                            <label for="website">Website</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form-group full">
                            <label style="display:flex;align-items:flex-start;gap:0.5rem;font-weight:normal;color:#374151;">
                                <input type="checkbox" name="data_confirmed" value="1" @checked(old('data_confirmed')) required style="margin-top:0.25rem;">
                                Saya menyatakan bahwa data yang saya isi sudah benar dan sesuai dengan dokumen pendukung.
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top:1rem;">{{ $config['submit_label'] }}</button>
                </form>
            </div>
        </div>
    </div>
</section>

@include('partials.service-cards')
@endsection
