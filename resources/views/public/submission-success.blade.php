@extends('layouts.app')

@section('title', 'Pengajuan Berhasil - Layanan Akademik')

@section('content')
<div class="page-title-bar">Pengajuan Berhasil</div>

<section class="container" style="padding:2rem 0 3rem;">
    <div class="form-card">
        <div class="form-card-header">Pengajuan Telah Diterima</div>
        <div style="padding:1.5rem;">
            <div class="alert alert-success">
                Pengajuan {{ $submission->serviceType->name }} Anda telah berhasil dikirim.
            </div>

            <table class="table">
                <tr><th>Nomor Tiket</th><td><strong>{{ $submission->ticket_number }}</strong></td></tr>
                <tr><th>Token Progres</th><td><strong style="word-break:break-all;">{{ $submission->progress_token }}</strong></td></tr>
                <tr><th>Nama</th><td>{{ $submission->full_name }}</td></tr>
                <tr><th>Email</th><td>{{ $submission->email }}</td></tr>
                <tr><th>Status</th><td>{{ $submission->status }}</td></tr>
            </table>

            <div class="alert alert-info">
                Simpan nomor tiket pengajuan Anda. Silakan cek Progres Layanan secara berkala. Jika pengajuan sudah selesai diproses, Anda akan diminta mengisi Survei Kepuasan Layanan Akademik sebelum mengunduh file hasil layanan.
            </div>

            <a href="{{ route('progress.index') }}" class="btn-sm btn-teal">Cek Progres Layanan</a>
            <a href="{{ route('survey.create', ['ticket' => $submission->ticket_number]) }}" class="btn-sm btn-green" style="margin-left:0.5rem;">Isi Survei Kepuasan</a>
            <a href="{{ route('beranda') }}" class="btn-sm btn-gray" style="margin-left:0.5rem;">Kembali ke Beranda</a>

            <div style="margin-top:1.5rem;">
                <p>Bantu kami meningkatkan kualitas layanan akademik dengan mengisi survei kepuasan layanan.</p>
            </div>

            <div style="margin-top:2rem;">
                @include('partials.admin-contact-support')
            </div>
        </div>
    </div>
</section>
@endsection
