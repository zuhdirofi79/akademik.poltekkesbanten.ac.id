@extends('layouts.app')

@section('title', 'Wisuda - Pemesanan Toga Wisuda Poltekkes Kemenkes Banten')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/wisuda.css') }}?v=20260904-pdp1">
@endpush

@section('content')
{{-- CSS khusus wisuda: selalu lewat asset() lokal, tidak bergantung CSS_URL --}}
<link rel="stylesheet" href="{{ asset('css/wisuda.css') }}?v=20260904-pdp1">
<style>
/* Critical fallback jika file CSS belum ter-cache: paksa layout 2 kolom */
.wisuda-page{background:#f5f5f5;padding:24px 0 48px;width:100%}
.wisuda-container{max-width:1188px;margin:0 auto;padding:0 16px;width:100%}
.wisuda-product-card{background:#fff;border-radius:8px;padding:24px;display:grid;grid-template-columns:480px minmax(0,1fr);gap:28px;box-shadow:0 1px 2px rgba(0,0,0,.08);align-items:start}
.wisuda-main-image{width:100%;height:460px;border:1px solid #eee;border-radius:8px;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden}
.wisuda-main-image img{width:100%;height:100%;object-fit:contain;display:block}
.wisuda-thumbnails{margin-top:12px;display:flex;gap:8px;overflow-x:auto}
.wisuda-thumbnail{width:64px;height:64px;border:1px solid #ddd;border-radius:6px;cursor:pointer;background:#fff;flex:0 0 auto;padding:4px}
.wisuda-thumbnail img{width:100%;height:100%;object-fit:contain;display:block}
.wisuda-thumbnail.active{border:2px solid #0ea5b7}
.wisuda-detail-section,.wisuda-checkout-card{margin-top:20px;background:#fff;border-radius:8px;box-shadow:0 1px 2px rgba(0,0,0,.08)}
.wisuda-checkout-card{padding:22px 24px}
@media (max-width:900px){.wisuda-product-card{grid-template-columns:1fr}.wisuda-main-image{height:360px}}
</style>
@php
    $gallery = [
        ['file' => 'Baju-Toga-Mahasiswa.jpeg', 'label' => 'Baju Toga Mahasiswa', 'prodi' => null],
        ['file' => 'Topi-Toga-Segi-Lima.jpeg', 'label' => 'Topi Toga Segi Lima', 'prodi' => null],
        ['file' => 'sleber-kerah-wisuda.jpeg', 'label' => 'Slebber / Kerah Wisuda', 'prodi' => null],
        ['file' => 'kerah-dan-kalung-wisuda-keperawatan.jpeg', 'label' => 'Kerah & Kalung Keperawatan', 'prodi' => 'keperawatan'],
        ['file' => 'kerah-dan-kalung-wisuda-kebidanan.jpeg', 'label' => 'Kerah & Kalung Kebidanan', 'prodi' => 'kebidanan'],
        ['file' => 'kerah-dan-kalung-wisuda-tlm.jpeg', 'label' => 'Kerah & Kalung TLM', 'prodi' => 'tlm'],
        ['file' => 'kalung-wisuda-keperawatan.jpeg', 'label' => 'Kalung Keperawatan', 'prodi' => 'keperawatan'],
        ['file' => 'kalung-wisuda-kebidanan.jpeg', 'label' => 'Kalung Kebidanan', 'prodi' => 'kebidanan'],
        ['file' => 'kalung-wisuda-tlm.jpeg', 'label' => 'Kalung TLM', 'prodi' => 'tlm'],
    ];

    $prodiMap = [
        'keperawatan' => [
            'label' => 'Keperawatan',
            'note' => 'Atribut kerah dan kalung sesuai Jurusan Keperawatan.',
            'primary' => 'kerah-dan-kalung-wisuda-keperawatan.jpeg',
        ],
        'kebidanan' => [
            'label' => 'Kebidanan',
            'note' => 'Atribut kerah dan kalung sesuai Jurusan Kebidanan.',
            'primary' => 'kerah-dan-kalung-wisuda-kebidanan.jpeg',
        ],
        'tlm' => [
            'label' => 'TLM',
            'note' => 'Atribut kerah dan kalung sesuai Jurusan Teknologi Laboratorium Medis.',
            'primary' => 'kerah-dan-kalung-wisuda-tlm.jpeg',
        ],
    ];

    $defaultFile = 'Baju-Toga-Mahasiswa.jpeg';
    $defaultSrc = asset('src/img/wisuda/'.$defaultFile);
@endphp

<div class="wisuda-page">
    <div class="wisuda-container">
        <div class="wisuda-product-shell">
            <article class="wisuda-product-card" id="wisudaProductPage">
                {{-- LEFT: gallery only --}}
                <div class="wisuda-gallery">
                    <div class="wisuda-main-image">
                        <img
                            id="wisudaMainImage"
                            src="{{ $defaultSrc }}"
                            alt="Baju Toga Mahasiswa Poltekkes Kemenkes Banten"
                        >
                    </div>
                    <div class="wisuda-thumbnails" id="wisudaThumbnails" role="list">
                        @foreach($gallery as $index => $item)
                            <button
                                type="button"
                                class="wisuda-thumbnail {{ $index === 0 ? 'active' : '' }}"
                                data-src="{{ asset('src/img/wisuda/'.$item['file']) }}"
                                data-file="{{ $item['file'] }}"
                                data-label="{{ $item['label'] }}"
                                data-prodi="{{ $item['prodi'] }}"
                                aria-label="{{ $item['label'] }}"
                            >
                                <img src="{{ asset('src/img/wisuda/'.$item['file']) }}" alt="{{ $item['label'] }}" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- RIGHT: product detail --}}
                <div class="wisuda-product-info">
                    <div class="wisuda-product-badges">
                        <span class="wisuda-badge">Resmi Poltekkes Kemenkes Banten</span>
                        <span class="wisuda-badge wisuda-badge-warn">Pemesanan minimal 10 hari sebelum wisuda</span>
                        <span class="wisuda-badge">Pelayanan 08.00–17.00 WIB</span>
                    </div>

                    <h1 class="wisuda-product-title">Paket Baju Toga Wisuda Poltekkes Kemenkes Banten</h1>
                    <p class="wisuda-product-subtitle">
                        Toga wisuda resmi mahasiswa Poltekkes Kemenkes Banten Tahun {{ $config['event_year'] }}.
                    </p>
                    <p class="wisuda-rating-line">★★★★★ 5.0 | Produk resmi Wisuda Poltekkes Kemenkes Banten</p>

                    <div class="wisuda-price-box">
                        <p class="wisuda-price-main">Biaya: Hubungi Tim Sekretariat</p>
                        <p class="wisuda-price-note">Informasi rekening pembayaran akan dikonfirmasi oleh Tim Sekretariat.</p>
                    </div>

                    <ul class="wisuda-meta-list">
                        <li><strong>Pelaksanaan:</strong> {{ $config['event_date'] }}</li>
                        <li><strong>Lokasi:</strong> {{ $config['event_location'] }}</li>
                        <li><strong>Pengambilan/Pemesanan:</strong> Silakan menghubungi Tim Sekretariat.</li>
                    </ul>

                    <div class="wisuda-option-group">
                        <div class="wisuda-option-label">
                            <span>Program Studi</span>
                            <small id="wisudaProdiNote">Pilih prodi untuk melihat atribut resmi.</small>
                        </div>
                        <div class="wisuda-option-buttons" id="wisudaProdiButtons">
                            @foreach($prodiMap as $key => $prodi)
                                <button
                                    type="button"
                                    class="wisuda-option-btn"
                                    data-prodi="{{ $key }}"
                                    data-primary="{{ asset('src/img/wisuda/'.$prodi['primary']) }}"
                                    data-file="{{ $prodi['primary'] }}"
                                    data-note="{{ $prodi['note'] }}"
                                    data-label="{{ $prodi['label'] }}"
                                >{{ $prodi['label'] }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="wisuda-option-group">
                        <div class="wisuda-option-label"><span>Ukuran</span></div>
                        <div class="wisuda-option-buttons">
                            <span class="wisuda-option-btn active" style="cursor:default;">All Size</span>
                        </div>
                    </div>

                    <div class="wisuda-option-group">
                        <div class="wisuda-option-label"><span>Jumlah</span></div>
                        <div class="wisuda-qty-wrap">
                            <button type="button" class="wisuda-qty-btn" id="wisudaQtyMinus" aria-label="Kurangi">−</button>
                            <input type="number" class="wisuda-qty-input" id="wisudaQtyInput" value="1" min="1" max="20">
                            <button type="button" class="wisuda-qty-btn" id="wisudaQtyPlus" aria-label="Tambah">+</button>
                        </div>
                    </div>

                    <div class="wisuda-cta-row">
                        <a href="#wisuda-checkout" class="wisuda-btn-primary" id="wisudaOrderCta">Pesan Baju Toga Sekarang</a>
                        <a href="#tab-kontak" class="wisuda-btn-outline" id="wisudaContactCta">Hubungi Tim Sekretariat</a>
                    </div>

                    <div class="wisuda-seller-box">
                        <div>
                            <strong>Tim Sekretariat</strong>
                            <span>{{ $config['contact_name'] }} · Pelayanan {{ $config['service_hours'] }}</span>
                        </div>
                        <div class="wisuda-option-buttons">
                            <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="wisuda-btn-outline" style="padding:8px 12px;font-size:13px;">Hubungi Tim Sekretariat</a>
                            <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="wisuda-btn-outline" style="padding:8px 12px;font-size:13px;">Kontak Alternatif</a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Detail tabs: NO large stacked images --}}
            <section class="wisuda-detail-section" id="wisudaDetailSection">
                <div class="wisuda-tabs" role="tablist">
                    <button type="button" class="wisuda-tab-btn active" data-tab="deskripsi">Deskripsi Produk</button>
                    <button type="button" class="wisuda-tab-btn" data-tab="isi-paket">Isi Paket</button>
                    <button type="button" class="wisuda-tab-btn" data-tab="ketentuan">Ketentuan Wisuda</button>
                    <button type="button" class="wisuda-tab-btn" data-tab="kontak" id="tab-kontak">Kontak Sekretariat</button>
                </div>
                <div class="wisuda-tab-panels">
                    <div class="wisuda-tab-panel active" data-panel="deskripsi">
                        <p>
                            Paket Baju Toga Wisuda kini bisa dipesan melalui Dharma Wanita Persatuan Poltekkes Kemenkes Banten.
                        </p>
                        <p>
                            Gambar di galeri adalah referensi resmi atribut toga (jubah, topi, slebber/kerah, serta kerah &amp; kalung per program studi).
                            Pilih thumbnail atau program studi untuk melihat atribut yang sesuai.
                        </p>
                    </div>
                    <div class="wisuda-tab-panel" data-panel="isi-paket">
                        <ul>
                            <li>Jubah/Toga Wisuda</li>
                            <li>Topi/Toga Cap</li>
                            <li>Kalung/Samir sesuai ketentuan program studi</li>
                            <li>Slebber/Kerah Wisuda</li>
                            <li>Atribut wisuda lain sesuai ketentuan Poltekkes Kemenkes Banten</li>
                        </ul>
                    </div>
                    <div class="wisuda-tab-panel" data-panel="ketentuan">
                        <ul>
                            <li>Wisuda Poltekkes Kemenkes Banten Tahun {{ $config['event_year'] }} dilaksanakan di Gedung Auditorium UIN SMH Banten pada tanggal {{ $config['event_date'] }}.</li>
                            <li>Peserta wisuda adalah mahasiswa yang telah dinyatakan lulus secara akademik, telah mengikuti seluruh rangkaian akademik, telah menyelesaikan/lulus Uji Kompetensi Nasional, telah melaksanakan yudisium, ditetapkan melalui Keputusan Direktur, serta telah menyelesaikan kewajiban akademik dan administrasi.</li>
                            <li>Wisudawati mengenakan kebaya dan kain.</li>
                            <li>Wisudawan mengenakan kemeja polos berkerah.</li>
                            <li>Penggunaan baju toga wisuda telah ditetapkan secara resmi sesuai ketentuan Poltekkes Kemenkes Banten, dengan pemakaian secara lengkap, rapi, dan sesuai tata cara pemakaian.</li>
                            <li>Tidak diperkenankan melakukan modifikasi toga dan atribut wisuda.</li>
                            <li>Pemesanan minimal 10 hari sebelum wisuda dilaksanakan.</li>
                        </ul>
                    </div>
                    <div class="wisuda-tab-panel" data-panel="kontak">
                        <p>Bila ada pertanyaan, silakan menghubungi Tim Sekretariat.</p>
                        <p>Pelayanan pukul 08.00–17.00 WIB. Di luar jam tersebut, silahkan menunggu hari berikutnya.</p>
                        <p>{{ $config['order_deadline_note'] }}</p>
                        <div class="wisuda-contact-inline">
                            <strong>{{ $config['contact_name'] }}</strong>
                            <p style="margin:6px 0;">{{ $config['contact_phone_primary'] }}</p>
                            <p style="margin:6px 0;">atau {{ $config['contact_phone_secondary'] }}</p>
                            <div class="wisuda-cta-row" style="margin-top:10px;">
                                <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="wisuda-btn-primary">Hubungi Tim Sekretariat</a>
                                <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="wisuda-btn-outline">Kontak Alternatif</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Checkout form --}}
            <section class="wisuda-checkout-card" id="wisuda-checkout">
                <h2>Checkout Pemesanan Toga</h2>
                <p style="margin:0;color:#6b7280;">Lengkapi data pemesanan. Setelah submit, Anda mendapat kode pesanan dan dapat mengunggah bukti pembayaran setelah mendapat info rekening dari Tim Sekretariat.</p>

                <div class="wisuda-checkout-summary" id="wisudaCheckoutSummary">
                    Ringkasan pilihan: <strong id="summaryProdi">Belum dipilih</strong> ·
                    Ukuran <strong id="summarySize">All Size</strong> ·
                    Jumlah <strong id="summaryQty">1</strong>
                </div>

                @if($errors->any())
                    <div class="alert alert-error" style="margin-bottom:1rem;">
                        <ul style="margin:0;padding-left:1.1rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('wisuda.order.store') }}" id="wisudaCheckoutForm">
                    @csrf
                    <input type="hidden" name="toga_size" id="formTogaSize" value="All Size">
                    <input type="hidden" name="quantity" id="formQuantity" value="{{ old('quantity', 1) }}">

                    <div class="wisuda-checkout-grid">
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
                                    <option value="{{ $program->id }}"
                                        data-name="{{ strtolower($program->name) }}"
                                        @selected(old('study_program_id') == $program->id)
                                    >{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="graduation_year">Tahun Lulus <span style="color:#dc2626;">*</span></label>
                            <input
                                type="number"
                                name="graduation_year"
                                id="graduation_year"
                                class="form-control"
                                value="{{ old('graduation_year') }}"
                                placeholder="Contoh: 2026"
                                min="2000"
                                max="2099"
                                required
                            >
                            @error('graduation_year')
                                <div style="color:#dc2626;font-size:0.875rem;margin-top:0.25rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span style="color:#dc2626;">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Nomor HP/Telepon <span style="color:#dc2626;">*</span></label>
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
                            <label for="toga_size_display">Ukuran Toga</label>
                            <input type="text" id="toga_size_display" class="form-control" value="All Size" readonly>
                        </div>
                        <div class="form-group full">
                            <label for="size_note">Catatan Tambahan</label>
                            <textarea name="size_note" id="size_note" class="form-control" rows="3" placeholder="Opsional: catatan tambahan untuk Tim Sekretariat.">{{ old('size_note') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="captcha_answer">Captcha: {{ $captcha['question'] }} <span style="color:#dc2626;">*</span></label>
                            <input type="number" name="captcha_answer" id="captcha_answer" class="form-control" required>
                            <input type="hidden" name="captcha_expected" value="{{ $captcha['expected'] }}">
                        </div>
                        <div class="form-group full">
                            <label style="display:flex;align-items:flex-start;gap:0.5rem;font-weight:500;">
                                <input type="checkbox" name="confirm" value="1" {{ old('confirm') ? 'checked' : '' }} style="margin-top:0.25rem;" required>
                                <span>Saya menyatakan data pemesanan toga wisuda yang diisi sudah benar.</span>
                            </label>
                        </div>
                    </div>

                    <div class="wisuda-cta-row">
                        <button type="submit" class="wisuda-btn-primary">Kirim Pemesanan</button>
                        <a href="{{ route('wisuda.order.create') }}" class="wisuda-btn-outline">Buka Form Terpisah</a>
                    </div>
                </form>

                <div class="wisuda-contact-inline">
                    <strong>Butuh info rekening?</strong>
                    <p style="margin:6px 0;">Untuk informasi rekening pembayaran, silakan hubungi Tim Sekretariat.</p>
                    <div class="wisuda-cta-row" style="margin-top:8px;">
                        <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="wisuda-btn-outline">Hubungi Tim Sekretariat</a>
                        <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="wisuda-btn-outline">Kontak Alternatif</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
(function () {
    var mainImage = document.getElementById('wisudaMainImage');
    var thumbs = Array.prototype.slice.call(document.querySelectorAll('.wisuda-thumbnail'));
    var prodiButtons = Array.prototype.slice.call(document.querySelectorAll('#wisudaProdiButtons .wisuda-option-btn'));
    var qtyInput = document.getElementById('wisudaQtyInput');
    var formSize = document.getElementById('formTogaSize');
    var formQty = document.getElementById('formQuantity');
    var summaryProdi = document.getElementById('summaryProdi');
    var summarySize = document.getElementById('summarySize');
    var summaryQty = document.getElementById('summaryQty');
    var prodiNote = document.getElementById('wisudaProdiNote');
    var studySelect = document.getElementById('study_program_id');
    var selectedProdi = '';
    var selectedSize = 'All Size';

    function setMain(src, file) {
        if (!mainImage || !src) return;
        mainImage.src = src;
        thumbs.forEach(function (thumb) {
            var active = thumb.getAttribute('data-src') === src || thumb.getAttribute('data-file') === file;
            thumb.classList.toggle('active', active);
        });
    }

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            setMain(thumb.getAttribute('data-src'), thumb.getAttribute('data-file'));
        });
    });

    function matchStudyProgram(prodiKey) {
        if (!studySelect) return;
        var options = Array.prototype.slice.call(studySelect.options);
        var found = options.find(function (opt) {
            var name = (opt.getAttribute('data-name') || '').toLowerCase();
            if (!prodiKey) return false;
            if (prodiKey === 'tlm') {
                return name.indexOf('tlm') !== -1 || name.indexOf('laboratorium') !== -1;
            }
            return name.indexOf(prodiKey) !== -1;
        });
        if (found) studySelect.value = found.value;
    }

    function updateSummary() {
        if (summaryProdi) summaryProdi.textContent = selectedProdi || 'Belum dipilih';
        if (summarySize) summarySize.textContent = 'All Size';
        if (summaryQty) summaryQty.textContent = qtyInput ? qtyInput.value : '1';
        if (formSize) formSize.value = 'All Size';
        if (formQty) formQty.value = qtyInput ? qtyInput.value : '1';
    }

    prodiButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            prodiButtons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            selectedProdi = btn.getAttribute('data-label') || '';
            if (prodiNote) prodiNote.textContent = btn.getAttribute('data-note') || '';
            setMain(btn.getAttribute('data-primary'), btn.getAttribute('data-file'));
            matchStudyProgram(btn.getAttribute('data-prodi'));
            updateSummary();
        });
    });

    function clampQty(v) {
        v = parseInt(v, 10);
        if (isNaN(v) || v < 1) v = 1;
        if (v > 20) v = 20;
        return v;
    }

    var minus = document.getElementById('wisudaQtyMinus');
    var plus = document.getElementById('wisudaQtyPlus');
    if (minus && qtyInput) {
        minus.addEventListener('click', function () {
            qtyInput.value = clampQty((parseInt(qtyInput.value, 10) || 1) - 1);
            updateSummary();
        });
    }
    if (plus && qtyInput) {
        plus.addEventListener('click', function () {
            qtyInput.value = clampQty((parseInt(qtyInput.value, 10) || 1) + 1);
            updateSummary();
        });
    }
    if (qtyInput) {
        qtyInput.addEventListener('change', function () {
            qtyInput.value = clampQty(qtyInput.value);
            updateSummary();
        });
    }

    document.querySelectorAll('.wisuda-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.getAttribute('data-tab');
            document.querySelectorAll('.wisuda-tab-btn').forEach(function (b) { b.classList.remove('active'); });
            document.querySelectorAll('.wisuda-tab-panel').forEach(function (p) { p.classList.remove('active'); });
            btn.classList.add('active');
            var panel = document.querySelector('.wisuda-tab-panel[data-panel="' + tab + '"]');
            if (panel) panel.classList.add('active');
        });
    });

    var contactCta = document.getElementById('wisudaContactCta');
    if (contactCta) {
        contactCta.addEventListener('click', function (e) {
            e.preventDefault();
            var kontakBtn = document.querySelector('.wisuda-tab-btn[data-tab="kontak"]');
            if (kontakBtn) kontakBtn.click();
            var detail = document.getElementById('wisudaDetailSection');
            if (detail) detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    var orderCta = document.getElementById('wisudaOrderCta');
    if (orderCta) {
        orderCta.addEventListener('click', function (e) {
            e.preventDefault();
            updateSummary();
            var checkout = document.getElementById('wisuda-checkout');
            if (checkout) checkout.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    @if($errors->any())
    var checkoutOnError = document.getElementById('wisuda-checkout');
    if (checkoutOnError) checkoutOnError.scrollIntoView({ behavior: 'smooth', block: 'start' });
    @endif

    updateSummary();
})();
</script>
@endsection
