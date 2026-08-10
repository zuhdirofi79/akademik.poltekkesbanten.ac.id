@extends('layouts.app')

@section('title', 'Beranda - Layanan Akademik Poltekkes Kemenkes Banten')

@section('content')
@php
    $heroGedungUrl = asset('src/img/dashboard-gedung-poltekkesbanten.png') . '?v=20260609-2';
@endphp

<section class="hero-home" style="--hero-bg: url('{{ $heroGedungUrl }}');">
    <div class="hero-home-overlay">
        <div class="container hero-home-content">
            <h1>Selamat Datang di Layanan Akademik Poltekkes Kemenkes Banten</h1>
            <p>Mudah, Cepat, Transparan, dan Terpercaya.</p>
        </div>
    </div>
</section>

@include('partials.service-reminder-beranda')
@include('partials.queue-cta')
@include('partials.survey-cta')

<section class="home-intro-section">
    <div class="container">
        <div class="home-intro-grid">
            <div class="campus-image-card">
                <img
                    src="{{ $heroGedungUrl }}"
                    alt="Gedung Direktorat Poltekkes Kemenkes Banten"
                    class="home-building-image"
                    loading="eager"
                />
            </div>

            <div class="service-highlight-card">
                <h2>Layanan Akademik Poltekkes Kemenkes Banten</h2>
                <p>Silakan melakukan usulan layanan akademik sesuai kebutuhan, termasuk Perubahan Data Mahasiswa Aktif, Surat Keterangan Aktif Kuliah, Surat Cuti/Keluar, Beasiswa, Berkelakuan Baik, Verifikasi Ijazah Alumni, Surat Rekomendasi, Layanan SIAKAD, Surat Tugas Mahasiswa, Nota Dinas Dispen Kegiatan Mahasiswa, dan Pengaktifan Kembali Mahasiswa Cuti/Keluar.</p>

                <ul class="service-check-list">
                    @foreach($services as $service)
                        <li>
                            <a href="{{ url('/layanan/' . $service->slug) }}">{{ $service->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="services-section">
    <div class="container services-grid">
        @php
            $icons = [
                'perubahan-data-mahasiswa-aktif' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'surat-keterangan-aktif-kuliah' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'surat-cuti-mahasiswa' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'surat-keluar-mahasiswa' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
                'surat-keterangan-beasiswa' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'surat-keterangan-berkelakuan-baik' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'surat-keterangan-verifikasi-ijazah-alumni' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                'surat-rekomendasi' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                'siakad' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                'surat-tugas-mahasiswa' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                'nota-dinas-dispen-kegiatan-mahasiswa' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'pengaktifan-kembali-mahasiswa' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            ];
        @endphp
        @foreach($services as $service)
            <article class="service-card">
                <svg class="service-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icons[$service->slug] ?? $icons['siakad'] }}" />
                </svg>
                <h3>{{ $service->name }}</h3>
                <p>{{ $service->description }}</p>
                <a href="{{ url('/layanan/' . $service->slug) }}">Ajukan Layanan →</a>
            </article>
        @endforeach
    </div>
</section>
@endsection
