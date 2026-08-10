@extends('layouts.app')

@section('title', 'Tiket Antrean '.$queue->queue_code)

@section('content')
<div class="page-title-bar">Nomor Antrean Anda</div>

<section class="container" style="padding:2rem 1rem 3rem;">
    <div class="form-card" style="max-width:640px;margin:0 auto;">
        <div class="form-card-header">Tiket Antrean Layanan Akademik</div>
        <div class="form-card-body" style="display:block;text-align:center;">
            <p style="margin:0;color:#64748b;">{{ $queue->queue_date->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}</p>
            <div style="font-size:4rem;font-weight:800;color:#0f766e;line-height:1.1;margin:1rem 0;">{{ $queue->queue_code }}</div>
            <p style="margin:0;"><span class="status-badge">{{ $queue->statusLabel() }}</span></p>
            <table class="table" style="margin-top:1.5rem;text-align:left;">
                <tr><th>Jam Ambil</th><td>{{ $queue->created_at->timezone('Asia/Jakarta')->format('H:i:s') }}</td></tr>
                @if($queue->visitor_name)
                    <tr><th>Nama</th><td>{{ $queue->visitor_name }}</td></tr>
                @endif
                @if($queue->visitor_type)
                    <tr><th>Jenis Pengunjung</th><td>{{ $queue->visitor_type }}</td></tr>
                @endif
                @if($queue->purposeDisplay() !== '-')
                    <tr><th>Keperluan</th><td>{{ $queue->purposeDisplay() }}</td></tr>
                @endif
                <tr><th>Antrean di depan</th><td>{{ $waitingAhead }} nomor</td></tr>
            </table>
            <p style="margin-top:1rem;color:#475569;">Mohon menunggu hingga nomor antrean Anda dipanggil.</p>
            <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;margin-top:1.5rem;">
                <a href="{{ route('queues.print', $queue) }}" class="btn-primary" target="_blank">Cetak Nomor Antrean</a>
                <a href="{{ route('queues.index') }}" class="btn-sm btn-gray">Kembali ke Halaman Antrean</a>
                <a href="{{ route('queues.display') }}" class="btn-sm btn-teal" target="_blank">Lihat Display</a>
            </div>
        </div>
    </div>
</section>
@endsection
