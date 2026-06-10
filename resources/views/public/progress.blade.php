@extends('layouts.app')

@section('title', 'Progres Layanan - Layanan Akademik Poltekkes Kemenkes Banten')

@section('content')
<div class="page-title-bar">Progres Layanan</div>

<section class="container form-layout">
    <div class="form-card" style="grid-column: 1 / -1;">
        <div class="form-card-header">Cek Progres Pengajuan</div>
        <div class="form-card-body" style="grid-template-columns: 1fr 1.2fr;">
            <div class="form-sidebar">
                <h3>Informasi Progres Layanan</h3>
                <p>Masukkan NIM atau email aktif beserta nomor tiket atau token progres yang Anda terima setelah pengajuan.</p>
                <p>Simpan nomor tiket dan token dengan aman. Jangan bagikan kepada pihak yang tidak berwenang.</p>

                @if(isset($serviceStats) && $serviceStats->isNotEmpty())
                    <div class="progress-box" style="margin-top:1.5rem;">
                        <strong>Data/Jumlah Layanan {{ now()->year }}</strong>
                        @foreach($serviceStats as $stat)
                            <div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid #e5e7eb;">
                                <p style="margin:0;font-weight:600;font-size:0.9rem;">{{ $stat['service']->name }}</p>
                                <p style="margin:0.25rem 0 0;font-size:0.85rem;">Total: <strong>{{ $stat['total'] }}</strong></p>
                                <p style="margin:0.15rem 0 0;font-size:0.85rem;">Diproses: <strong>{{ $stat['processed'] }}</strong> | Belum: <strong>{{ $stat['pending'] }}</strong></p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @include('partials.admin-contact-support')
            </div>
            <div class="form-main">
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('progress.check') }}">
                    @csrf
                    <div class="form-group">
                        <label for="identifier">NIM / Email</label>
                        <input type="text" name="identifier" id="identifier" class="form-control" value="{{ old('identifier') }}" required placeholder="Masukkan NIM atau email">
                        @error('identifier')<small style="color:#dc2626;">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="progress_token">Nomor Tiket / Token</label>
                        <input type="text" name="progress_token" id="progress_token" class="form-control" value="{{ old('progress_token') }}" required placeholder="Contoh: AKD-2026-000001 atau token">
                        @error('progress_token')<small style="color:#dc2626;">{{ $message }}</small>@enderror
                    </div>
                    <button type="submit" class="btn-primary">Cek Progres</button>
                </form>

                @if($submission)
                    <hr style="margin:2rem 0;border:none;border-top:1px solid #e5e7eb;">
                    <h3>Detail Pengajuan</h3>
                    <table class="table">
                        <tr><th>Nomor Tiket</th><td>{{ $submission->ticket_number }}</td></tr>
                        <tr><th>Layanan</th><td>{{ $submission->serviceType->name }}</td></tr>
                        <tr><th>Nama</th><td>{{ $submission->full_name }}</td></tr>
                        <tr><th>Program Studi</th><td>{{ $submission->studyProgram->name }}</td></tr>
                        <tr><th>Status</th><td><span class="status-badge">{{ $submission->status }}</span></td></tr>
                        <tr><th>Tanggal Pengajuan</th><td>{{ $submission->created_at->format('d/m/Y H:i') }}</td></tr>
                        @if($submission->admin_note)
                            <tr><th>Catatan Admin</th><td>{{ $submission->admin_note }}</td></tr>
                        @endif
                    </table>

                    @if($submission->canDownloadResults())
                        <div class="alert alert-success">
                            File hasil layanan sudah tersedia. Anda juga akan menerima file melalui email jika status sudah selesai.
                        </div>
                        <h4>Unduh File Final</h4>
                        <ul class="download-list">
                            @foreach($submission->finalFiles as $file)
                                <li>
                                    <a href="{{ route('download.file', ['submission' => $submission->id, 'file' => $file->id, 'token' => $submission->progress_token]) }}">
                                        Unduh {{ $file->original_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info">
                            File final belum tersedia. Pengajuan masih dalam proses. File akan dikirim ke email Anda setelah selesai diproses admin.
                        </div>
                    @endif

                    @if(! $submission->email_sent_at)
                        <div style="margin-top:1.5rem;">
                            @include('partials.admin-contact-support')
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
