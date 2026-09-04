@extends('layouts.app')

@section('title', 'Pesan Toga Wisuda - Poltekkes Kemenkes Banten')

@section('content')
<div class="page-title-bar">Pemesanan Toga Wisuda</div>

<section class="container form-layout">
    <div class="form-card" style="grid-column: 1 / -1;">
        <div class="form-card-header">Form Pemesanan Paket Toga Wisuda Mahasiswa</div>
        <div class="form-card-body" style="grid-template-columns: 1fr 1.4fr;">
            <div class="form-sidebar">
                <h3>Wisuda {{ $config['event_year'] }}</h3>
                <p>Pelaksanaan: <strong>{{ $config['event_date'] }}</strong></p>
                <p>Lokasi: <strong>{{ $config['event_location'] }}</strong></p>
                <p>{{ $config['order_deadline_note'] }}</p>
                <p>Isi data dengan benar. Setelah submit, Anda mendapat kode pesanan. Untuk info rekening, hubungi admin HUMAS lalu unggah bukti pembayaran.</p>
                <div class="wisuda-contact-box" style="margin-top:1rem;">
                    <strong>{{ $config['contact_name'] }}</strong>
                    <p style="margin:0.35rem 0;">{{ $config['contact_phone_primary'] }}</p>
                    <div class="wisuda-hero-actions">
                        <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-green">Hubungi Dias Arliadini</a>
                        <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-blue">Kontak Alternatif</a>
                    </div>
                </div>
                <a href="{{ route('wisuda.index') }}" class="btn-sm btn-gray" style="margin-top:0.75rem;display:inline-block;">Kembali ke Halaman Wisuda</a>
            </div>

            <div class="form-main">
                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin:0;padding-left:1.1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('wisuda.order.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="student_name">Nama Lengkap <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="student_name" id="student_name" class="form-control" value="{{ old('student_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nim">NIM <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="nim" id="nim" class="form-control" value="{{ old('nim') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nik">NIK <small>(opsional)</small></label>
                        <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}">
                    </div>
                    <div class="form-group">
                        <label for="study_program_id">Program Studi <span style="color:#dc2626;">*</span></label>
                        <select name="study_program_id" id="study_program_id" class="form-control" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($studyPrograms as $program)
                                @php
                                    $nameLower = strtolower($program->name);
                                    $hintMatched = filled($prodiHint ?? null) && (
                                        str_contains($nameLower, $prodiHint)
                                        || ($prodiHint === 'tlm' && (str_contains($nameLower, 'laboratorium') || str_contains($nameLower, 'tlm')))
                                    );
                                    $isSelected = old('study_program_id') == $program->id || (! old('study_program_id') && $hintMatched);
                                @endphp
                                <option value="{{ $program->id }}" @selected($isSelected)>{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span style="color:#dc2626;">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor HP / WhatsApp <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin <span style="color:#dc2626;">*</span></label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" @selected(old('gender') === 'Laki-laki')>Laki-laki</option>
                            <option value="Perempuan" @selected(old('gender') === 'Perempuan')>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="toga_size">Ukuran Toga <span style="color:#dc2626;">*</span></label>
                        <select name="toga_size" id="toga_size" class="form-control" required>
                            <option value="">-- Pilih Ukuran --</option>
                            @foreach(['S','M','L','XL','XXL','Custom'] as $size)
                                <option value="{{ $size }}" @selected(old('toga_size') === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="size_note">Catatan Ukuran</label>
                        <textarea name="size_note" id="size_note" class="form-control" rows="3" placeholder="Isi jika memilih ukuran custom atau ada catatan khusus.">{{ old('size_note') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Jumlah <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" max="20" required>
                    </div>
                    <div class="form-group">
                        <label for="captcha_answer">Captcha: {{ $captcha['question'] }} <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="captcha_answer" id="captcha_answer" class="form-control" required>
                        <input type="hidden" name="captcha_expected" value="{{ $captcha['expected'] }}">
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:flex-start;gap:0.5rem;font-weight:500;">
                            <input type="checkbox" name="confirm" value="1" {{ old('confirm') ? 'checked' : '' }} style="margin-top:0.25rem;" required>
                            <span>Saya menyatakan data pemesanan toga wisuda yang diisi sudah benar.</span>
                        </label>
                    </div>
                    <button type="submit" class="btn-primary" style="width:100%;padding:1rem;font-size:1.05rem;">Kirim Pemesanan</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
