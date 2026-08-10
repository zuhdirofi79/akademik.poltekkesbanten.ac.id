@extends('layouts.app')

@section('title', 'Antrean Layanan Akademik - Poltekkes Kemenkes Banten')

@section('content')
<div class="page-title-bar">Antrean Layanan Akademik</div>

<section class="container form-layout">
    <div class="form-card" style="grid-column: 1 / -1;">
        <div class="form-card-header">Ambil Nomor Antrean</div>
        <div class="form-card-body" style="grid-template-columns: 1fr 1.2fr;">
            <div class="form-sidebar">
                <h3>Pelayanan Langsung di Ruang Layanan Akademik</h3>
                <p>Silakan ambil nomor antrean untuk mendapatkan pelayanan langsung di ruang layanan akademik Poltekkes Kemenkes Banten.</p>
                <p><strong>Tanggal:</strong> {{ $todayLabel }}</p>
                <div class="progress-box" style="margin-top:1rem;">
                    <p style="margin:0;">Menunggu: <strong>{{ $stats['waiting_count'] }}</strong></p>
                    <p style="margin:0.35rem 0 0;">Sedang dipanggil/dilayani: <strong>{{ ($stats['called_count'] ?? 0) + ($stats['serving_count'] ?? 0) }}</strong></p>
                    <p style="margin:0.35rem 0 0;">Selesai hari ini: <strong>{{ $stats['served_count'] }}</strong></p>
                </div>
                <div style="margin-top:1.25rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <a href="{{ route('queues.display') }}" class="btn-sm btn-teal" target="_blank">Layar Display</a>
                    <a href="{{ route('queues.kiosk') }}" class="btn-sm btn-gray">Mode Kiosk</a>
                </div>
            </div>

            <div class="form-main">
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin:0;padding-left:1.1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('queues.take') }}" id="queueTakeForm">
                    @csrf
                    <div class="form-group">
                        <label for="visitor_name">Nama Pengunjung <small>(opsional)</small></label>
                        <input type="text" name="visitor_name" id="visitor_name" class="form-control" value="{{ old('visitor_name') }}" placeholder="Nama lengkap">
                    </div>
                    <div class="form-group">
                        <label for="visitor_identity">NIM / NIP / NIK <small>(opsional)</small></label>
                        <input type="text" name="visitor_identity" id="visitor_identity" class="form-control" value="{{ old('visitor_identity') }}" placeholder="NIM / NIP / NIK">
                    </div>
                    <div class="form-group">
                        <label for="visitor_phone">Nomor WhatsApp <small>(opsional)</small></label>
                        <input type="text" name="visitor_phone" id="visitor_phone" class="form-control" value="{{ old('visitor_phone') }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label for="visitor_type">Jenis Pengunjung <span style="color:#dc2626;">*</span></label>
                        <select name="visitor_type" id="visitor_type" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach($visitorTypes as $type)
                                <option value="{{ $type }}" @selected(old('visitor_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="purpose">Keperluan Layanan <small>(opsional)</small></label>
                        <select name="purpose" id="purpose" class="form-control">
                            <option value="">-- Pilih keperluan --</option>
                            @foreach($purposeOptions as $option)
                                <option value="{{ $option['value'] }}" @selected(old('purpose') === $option['value'])>{{ $option['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:flex-start;gap:0.5rem;font-weight:500;">
                            <input type="checkbox" name="confirm" value="1" {{ old('confirm') ? 'checked' : '' }} style="margin-top:0.25rem;">
                            <span>Saya yakin ingin mengambil nomor antrean untuk hari ini.</span>
                        </label>
                    </div>
                    <button type="submit" class="btn-primary" style="width:100%;padding:1rem;font-size:1.1rem;">Ambil Nomor Antrean</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('queueTakeForm')?.addEventListener('submit', function (e) {
    if (!confirm('Apakah Anda yakin ingin mengambil nomor antrean?')) {
        e.preventDefault();
    }
});
</script>
@endpush
