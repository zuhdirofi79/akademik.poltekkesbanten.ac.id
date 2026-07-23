@extends('layouts.app')

@section('title', $config['title'] . ' - Layanan Akademik')

@section('content')
@php
    $slug = $config['slug'];
    $semesterSlugs = \App\Support\ServiceFormConfig::semesterSlugs();
    $rekomendasiSlug = \App\Support\ServiceFormConfig::rekomendasiSlug();
    $activityGroupSlugs = \App\Support\ServiceFormConfig::activityGroupSlugs();
    $reactivationSlug = \App\Support\ServiceFormConfig::reactivationSlug();
    $isActivityGroup = in_array($slug, $activityGroupSlugs, true);
    $isReactivation = $slug === $reactivationSlug;
    $isAlumniVerifikasi = $slug === 'surat-keterangan-verifikasi-ijazah-alumni';
@endphp

<div class="page-title-bar">{{ $config['title'] }}</div>

<section class="container form-layout">
    <div class="form-card" style="grid-column: 1 / -1;">
        <div class="form-card-header">📄 {{ $config['card_title'] }}</div>
        <div class="form-card-body">
            <aside class="form-sidebar">
                <h3>{{ $config['info_title'] }}</h3>
                @if(in_array($slug, ['surat-cuti-mahasiswa', 'surat-keluar-mahasiswa']))
                    @php
                        $infoParts = explode("\n\n", $config['info_text'], 2);
                    @endphp
                    <p>{{ $infoParts[0] }}</p>
                    @if(isset($infoParts[1]))
                        <p>{{ trim($infoParts[1]) }}</p>
                    @endif
                    <div class="student-help-contact" style="margin-top:1rem;font-size:0.9rem;">
                        <p style="margin-bottom:0.5rem;"><strong>Kontak bantuan mahasiswa:</strong></p>
                        <p style="margin-bottom:0.5rem;">
                            Email Akademik:
                            <a href="mailto:{{ config('akademik.admin_contact.email') }}">{{ config('akademik.admin_contact.email') }}</a>
                        </p>
                        <p style="margin-bottom:0;">
                            <a href="{{ config('akademik.admin_contact.whatsapp_url') }}" target="_blank" rel="noopener noreferrer">Hubungi via WhatsApp</a>
                        </p>
                    </div>
                @elseif($isAlumniVerifikasi)
                    @php
                        $infoParts = preg_split("/\n\n+/", $config['info_text']) ?: [];
                        $adminEmail = config('akademik.admin_contact_email');
                    @endphp
                    @foreach($infoParts as $index => $part)
                        @if($index === 0)
                            <p style="font-weight:700;letter-spacing:0.02em;color:#0f766e;">{{ trim($part) }}</p>
                        @else
                            <p>{!! nl2br(e(trim($part))) !!}</p>
                        @endif
                    @endforeach
                    <p style="margin-top:0.75rem;">
                        Email admin akademik:
                        <a href="mailto:{{ $adminEmail }}"><strong>{{ $adminEmail }}</strong></a>
                    </p>
                @else
                    <p>{{ $config['info_text'] }}</p>
                @endif

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
                            <label for="nim">
                                @if($isActivityGroup)
                                    NIM Perwakilan
                                @else
                                    NIM
                                @endif
                            </label>
                            <input type="text" name="nim" id="nim" class="form-control" value="{{ old('nim') }}" required>
                        </div>

                        @unless($isActivityGroup)
                            <div class="form-group">
                                <label for="nik">NIK @if($isReactivation)<span style="font-weight:normal;color:#6b7280;">(opsional)</span>@endif</label>
                                <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" maxlength="16" @required(! $isReactivation)>
                            </div>
                        @endunless

                        <div class="form-group full">
                            <label for="full_name">
                                @if($isAlumniVerifikasi)
                                    Nama Lengkap Alumni
                                @elseif($isActivityGroup)
                                    Nama Lengkap Perwakilan
                                @else
                                    Nama Lengkap
                                @endif
                            </label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="study_program_id">
                                @if($isActivityGroup)
                                    Program Studi Perwakilan
                                @else
                                    Program Studi
                                @endif
                            </label>
                            <select name="study_program_id" id="study_program_id" class="form-select" required>
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($studyPrograms as $program)
                                    <option value="{{ $program->id }}" @selected(old('study_program_id') == $program->id)>{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                @if($isAlumniVerifikasi)
                                    Email Aktif
                                @elseif($slug === 'surat-rekomendasi')
                                    Email Mahasiswa/Alumni
                                @elseif($isActivityGroup)
                                    Email Perwakilan
                                @elseif($isReactivation)
                                    Email Mahasiswa Aktif
                                @else
                                    Email Mahasiswa
                                @endif
                            </label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Masukan Email Aktif" required>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp">
                                @if($isActivityGroup)
                                    Nomor WhatsApp Perwakilan
                                @else
                                    Nomor WhatsApp
                                @endif
                            </label>
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

                        @if($slug === 'surat-keterangan-aktif-kuliah')
                            <div class="form-group full">
                                <label for="active_krs">Data Dukung - KRS Periode Aktif</label>
                                <input type="file" name="active_krs" id="active_krs" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small>{{ $config['file_help']['active_krs'] ?? 'KRS periode aktif diunduh terlebih dahulu dari SIAKAD, kemudian diunggah pada form ini.' }}</small>
                            </div>
                            <div class="form-group full">
                                <label for="purpose">{{ $config['purpose_label'] ?? 'Keperluan Surat' }}</label>
                                <textarea name="purpose" id="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
                            </div>
                        @endif

                        @if($slug === 'surat-keterangan-berkelakuan-baik')
                            <div class="form-group full">
                                <label for="active_krs">Data Dukung - KRS Periode Aktif</label>
                                <input type="file" name="active_krs" id="active_krs" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="purpose">{{ $config['purpose_label'] ?? 'Keperluan Surat' }}</label>
                                <textarea name="purpose" id="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
                            </div>
                        @endif

                        @if($slug === $rekomendasiSlug)
                            <div class="form-group full">
                                <label for="alumni_supporting_data">DATA DUKUNG ALUMNI - SURAT PERNYATAAN MAGANG DAN SCAN IJAZAH</label>
                                <input type="file" name="alumni_supporting_data" id="alumni_supporting_data" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small>Unggah surat pernyataan magang dan scan ijazah dalam satu file PDF, atau unggah dokumen pendukung dalam format JPG/PNG.</small>
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

                        @if($slug === 'surat-keterangan-beasiswa')
                            <div class="form-group">
                                <label for="gpa_score">IPS/IPK</label>
                                <input type="text" name="gpa_score" id="gpa_score" class="form-control" value="{{ old('gpa_score') }}" placeholder="Contoh: 3.75" inputmode="decimal" required>
                            </div>
                            <div class="form-group full">
                                <label for="active_krs">Data Dukung - KRS Periode Aktif</label>
                                <input type="file" name="active_krs" id="active_krs" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="purpose">{{ $config['purpose_label'] ?? 'Keperluan Surat Beasiswa Apa?' }}</label>
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
                                <label for="statement_letter">Data Dukung - Surat Pernyataan Tertulis Bermaterai</label>
                                <input type="file" name="statement_letter" id="statement_letter" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small>Unggah surat pernyataan tertulis bermaterai dalam format PDF/JPG/PNG.</small>
                            </div>
                            <div class="form-group full">
                                <label for="leave_reason">Alasan Cuti</label>
                                <textarea name="leave_reason" id="leave_reason" class="form-control" rows="3" required>{{ old('leave_reason') }}</textarea>
                            </div>
                        @endif

                        @if($slug === 'surat-keluar-mahasiswa')
                            <div class="form-group full">
                                <label for="khs_active_proof">Data Dukung - Bukti KHS Aktif</label>
                                <input type="file" name="khs_active_proof" id="khs_active_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="statement_letter">Data Dukung - Surat Pernyataan Tertulis Bermaterai</label>
                                <input type="file" name="statement_letter" id="statement_letter" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small>Unggah surat pernyataan tertulis bermaterai dalam format PDF/JPG/PNG.</small>
                            </div>
                            <div class="form-group full">
                                <label for="exit_reason">Alasan Keluar</label>
                                <textarea name="exit_reason" id="exit_reason" class="form-control" rows="3" required>{{ old('exit_reason') }}</textarea>
                            </div>
                        @endif

                        @if($isAlumniVerifikasi)
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

                        @if($isActivityGroup)
                            <div class="form-group full">
                                <label for="activity_name">Nama Kegiatan</label>
                                <input type="text" name="activity_name" id="activity_name" class="form-control" value="{{ old('activity_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="activity_date">Tanggal Kegiatan <span style="font-weight:normal;color:#6b7280;">(opsional)</span></label>
                                <input type="date" name="activity_date" id="activity_date" class="form-control" value="{{ old('activity_date') }}">
                            </div>
                            <div class="form-group">
                                <label for="activity_location">Lokasi Kegiatan <span style="font-weight:normal;color:#6b7280;">(opsional)</span></label>
                                <input type="text" name="activity_location" id="activity_location" class="form-control" value="{{ old('activity_location') }}">
                            </div>
                            <div class="form-group full">
                                <label for="participant_list">Daftar Mahasiswa yang Mengikuti Kegiatan</label>
                                <textarea name="participant_list" id="participant_list" class="form-control" rows="5" placeholder="Tuliskan nama, NIM, dan prodi setiap mahasiswa. Satu baris untuk satu mahasiswa." required>{{ old('participant_list') }}</textarea>
                            </div>
                            @if($slug === 'surat-tugas-mahasiswa')
                                <div class="form-group full">
                                    <label for="proposal_or_invitation">Data Dukung - Proposal atau Surat Undangan</label>
                                    <input type="file" name="proposal_or_invitation" id="proposal_or_invitation" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                            @endif
                            @if($slug === 'nota-dinas-dispen-kegiatan-mahasiswa')
                                <div class="form-group full">
                                    <label for="invitation_or_proposal">Data Dukung - Bukti Undangan / Proposal</label>
                                    <input type="file" name="invitation_or_proposal" id="invitation_or_proposal" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                            @endif
                        @endif

                        @if($isReactivation)
                            <div class="form-group">
                                <label for="previous_status">Status Sebelumnya</label>
                                <select name="previous_status" id="previous_status" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    @foreach(($config['previous_status_options'] ?? ['Cuti', 'Keluar']) as $statusOption)
                                        <option value="{{ $statusOption }}" @selected(old('previous_status') == $statusOption)>{{ $statusOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="semester">Semester <span style="font-weight:normal;color:#6b7280;">(opsional)</span></label>
                                <input type="text" name="semester" id="semester" class="form-control" value="{{ old('semester') }}">
                            </div>
                            <div class="form-group full">
                                <label for="reactivation_reason">Alasan Pengaktifan Kembali <span style="font-weight:normal;color:#6b7280;">(opsional)</span></label>
                                <textarea name="reactivation_reason" id="reactivation_reason" class="form-control" rows="3">{{ old('reactivation_reason') }}</textarea>
                            </div>
                            <div class="form-group full">
                                <label for="reactivation_registration_proof">Data Dukung - Bukti Registrasi Cuti ke Akademik Jurusan</label>
                                <input type="file" name="reactivation_registration_proof" id="reactivation_registration_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-group full">
                                <label for="reactivation_statement_letter">Data Dukung - Surat Pernyataan Bermaterai Aktif Kembali</label>
                                <input type="file" name="reactivation_statement_letter" id="reactivation_statement_letter" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
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
