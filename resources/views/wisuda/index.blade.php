@extends('layouts.app')

@section('title', 'Wisuda - Pemesanan Toga Wisuda Poltekkes Kemenkes Banten')

@section('content')
@php
    $mockupUrl = asset('src/img/wisuda/toga-box-mockup.svg');
@endphp

<section class="wisuda-hero">
    <div class="container wisuda-hero-inner">
        <div class="wisuda-hero-copy">
            <div class="wisuda-badge-row">
                <span class="wisuda-badge">Wisuda Tahun {{ $config['event_year'] }}</span>
                <span class="wisuda-badge wisuda-badge-alt">Pelaksanaan: {{ $config['event_date'] }}</span>
                <span class="wisuda-badge wisuda-badge-soft">Lokasi: {{ $config['event_location'] }}</span>
            </div>
            <h1>Pemesanan Toga Wisuda Poltekkes Kemenkes Banten</h1>
            <p>
                Anda akan mengikuti wisuda? Kami menyediakan toga wisuda dengan bahan berkualitas
                dan sesuai ketentuan Poltekkes Kemenkes Banten.
            </p>
            <div class="wisuda-hero-actions">
                <a href="{{ route('wisuda.order.create') }}" class="btn-sm btn-teal">Pesan Toga Sekarang</a>
                <a href="#ketentuan-wisuda" class="btn-sm btn-blue">Lihat Ketentuan Wisuda</a>
            </div>
        </div>
        <div class="wisuda-hero-visual">
            <img
                src="{{ $mockupUrl }}"
                alt="Mockup Paket Toga Wisuda Poltekkes Kemenkes Banten"
                class="wisuda-mockup-image"
                loading="eager"
            >
        </div>
    </div>
</section>

<section class="wisuda-section">
    <div class="container">
        <article class="wisuda-product-card">
            <div class="wisuda-product-media">
                <img
                    src="{{ $mockupUrl }}"
                    alt="Mockup Paket Toga Wisuda Poltekkes Kemenkes Banten"
                    loading="lazy"
                >
            </div>
            <div class="wisuda-product-info">
                <span class="wisuda-product-badge">Resmi Poltekkes Kemenkes Banten</span>
                <h2>Paket Toga Wisuda Mahasiswa</h2>
                <p class="wisuda-product-highlight">
                    Bahan berkualitas, lengkap sesuai ketentuan, dan siap dipakai untuk prosesi wisuda
                    Tahun {{ $config['event_year'] }}.
                </p>
                <ul class="wisuda-product-points">
                    <li>Jubah/toga, topi/cap, samir prodi, dan slebber</li>
                    <li>Sesuai ketentuan busana akademik Poltekkes Kemenkes</li>
                    <li>Pemesanan melalui Dharma Wanita / Panitia Wisuda</li>
                    <li>Minimal 10 hari sebelum pelaksanaan wisuda</li>
                </ul>
                <div class="wisuda-hero-actions">
                    <a href="{{ route('wisuda.order.create') }}" class="btn-sm btn-teal">Pesan Sekarang</a>
                    <a href="#kontak-panitia" class="btn-sm btn-gray">Hubungi Panitia</a>
                </div>
            </div>
        </article>
    </div>
</section>

<section class="wisuda-section wisuda-section-alt">
    <div class="container">
        <h2 class="wisuda-section-title">Preview Samir Program Studi</h2>
        <div class="wisuda-preview-grid">
            <img src="{{ asset('src/img/wisuda/preview-keperawatan.svg') }}" alt="Preview Keperawatan" loading="lazy">
            <img src="{{ asset('src/img/wisuda/preview-kebidanan.svg') }}" alt="Preview Kebidanan" loading="lazy">
            <img src="{{ asset('src/img/wisuda/preview-tlm.svg') }}" alt="Preview TLM" loading="lazy">
        </div>
        <p class="wisuda-note">Ilustrasi sederhana untuk membantu mengenali aksen samir. Atribut final mengikuti ketentuan prodi masing-masing.</p>
    </div>
</section>

<section class="wisuda-section" id="isi-paket">
    <div class="container">
        <h2 class="wisuda-section-title">Isi Paket Toga Wisuda</h2>
        <div class="wisuda-package-grid">
            <div class="wisuda-package-item">Jubah/Toga Wisuda</div>
            <div class="wisuda-package-item">Topi/Toga Cap</div>
            <div class="wisuda-package-item">Kalung/Samir sesuai ketentuan program studi</div>
            <div class="wisuda-package-item">Slebber/Kerah Wisuda</div>
            <div class="wisuda-package-item">Atribut wisuda lain sesuai ketentuan Poltekkes Kemenkes Banten</div>
        </div>
    </div>
</section>

<section class="wisuda-section wisuda-section-alt" id="ketentuan-wisuda">
    <div class="container">
        <h2 class="wisuda-section-title">Ketentuan Wisuda</h2>
        <div class="wisuda-rules-card">
            <p>
                Wisuda Politeknik Kesehatan Kementerian Kesehatan Banten Tahun {{ $config['event_year'] }}
                dilaksanakan di Gedung Auditorium UIN SMH Banten pada tanggal {{ $config['event_date'] }}.
            </p>
            <ul class="wisuda-rules-list">
                <li>Peserta wisuda adalah mahasiswa yang telah dinyatakan lulus secara akademik, ditetapkan melalui Keputusan Direktur, serta telah menyelesaikan kewajiban akademik dan administrasi.</li>
                <li>Peserta wajib hadir sesuai waktu yang ditentukan panitia.</li>
                <li>Peserta wajib mengikuti rangkaian gladi bersih, technical meeting, pengarahan, dan prosesi wisuda.</li>
                <li>Peserta wajib menjaga ketertiban, kebersihan, keamanan, dan nama baik institusi.</li>
                <li>Wisudawan/wisudawati wajib mengenakan pakaian sopan dan rapi.</li>
                <li>Wisudawati mengenakan kebaya dan kain.</li>
                <li>Wisudawan mengenakan kemeja polos berkerah.</li>
                <li>Wajib menggunakan toga yang telah ditetapkan Poltekkes Kemenkes Banten secara lengkap, rapi, dan sesuai tata cara pemakaian.</li>
                <li>Tidak diperkenankan melakukan modifikasi toga dan atribut wisuda.</li>
                <li>Wisudawan/wisudawati dapat memesan baju toga mahasiswa ke Dharma Wanita Persatuan Poltekkes Kemenkes Banten.</li>
                <li>Pemesanan minimal 10 hari sebelum wisuda dilaksanakan.</li>
            </ul>
        </div>
    </div>
</section>

<section class="wisuda-section" id="kontak-panitia">
    <div class="container">
        <h2 class="wisuda-section-title">Informasi Pemesanan dan Kontak Panitia</h2>
        <div class="wisuda-contact-card">
            <p>Bagi yang berminat melakukan order toga wisuda, silakan melakukan pemesanan melalui halaman ini.</p>
            <p>Pelayanan pukul {{ $config['service_hours'] }}. Di luar jam tersebut, silakan menunggu hari berikutnya.</p>
            <p>{{ $config['order_deadline_note'] }}</p>
            <p>
                Bila ada pertanyaan silakan hubungi panitia pada jam pelayanan pukul {{ $config['service_hours'] }}.
                Di luar jam tersebut, silakan menunggu hari berikutnya. Pelayanan dibuka hingga minimal 10 hari sebelum wisuda dilaksanakan.
            </p>

            <div class="wisuda-contact-box">
                <strong>{{ $config['contact_name'] }}</strong>
                <p>Nomor: {{ $config['contact_phone_primary'] }}</p>
                <p>Nomor alternatif: {{ $config['contact_phone_secondary'] }}</p>
            </div>

            <div class="wisuda-hero-actions">
                <a href="{{ route('wisuda.order.create') }}" class="btn-sm btn-teal">Pesan Toga Sekarang</a>
                <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-green">Hubungi Dias Arliadini</a>
                <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-blue">Hubungi Kontak Alternatif</a>
            </div>
        </div>
    </div>
</section>
@endsection
