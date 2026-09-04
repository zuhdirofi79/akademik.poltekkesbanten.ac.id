@extends('layouts.app')

@section('title', 'Wisuda - Pemesanan Toga Wisuda Poltekkes Kemenkes Banten')

@section('content')
@php
    $imgBase = asset('src/img/wisuda');
    $gallery = collect($config['product_images'] ?? [])->map(function ($item) use ($imgBase) {
        return [
            'src' => $imgBase.'/'.$item['file'],
            'file' => $item['file'],
            'label' => $item['label'],
            'alt' => $item['alt'],
            'prodi' => $item['prodi'] ?? null,
        ];
    })->values();
    $defaultImage = $gallery->first();
    $variants = $config['prodi_variants'] ?? [];
@endphp

<div class="page-title-bar">Pemesanan Toga Wisuda</div>

<section class="wisuda-pdp-wrap">
    <div class="container">
        <div class="wisuda-pdp" id="wisudaProductPage"
             data-default-src="{{ $defaultImage['src'] ?? '' }}"
             data-default-alt="{{ $defaultImage['alt'] ?? 'Paket Toga Wisuda' }}">

            {{-- LEFT: Gallery --}}
            <div class="wisuda-pdp-gallery">
                <div class="wisuda-pdp-main">
                    <img
                        id="wisudaMainImage"
                        src="{{ $defaultImage['src'] ?? '' }}"
                        alt="{{ $defaultImage['alt'] ?? 'Baju Toga Mahasiswa Poltekkes Kemenkes Banten' }}"
                        loading="eager"
                    >
                </div>

                <div class="wisuda-pdp-thumbs-wrap">
                    <button type="button" class="wisuda-thumb-nav" id="wisudaThumbPrev" aria-label="Thumbnail sebelumnya">‹</button>
                    <div class="wisuda-pdp-thumbs" id="wisudaThumbTrack">
                        @foreach($gallery as $index => $image)
                            <button
                                type="button"
                                class="wisuda-thumb {{ $index === 0 ? 'is-active' : '' }}"
                                data-src="{{ $image['src'] }}"
                                data-alt="{{ $image['alt'] }}"
                                data-file="{{ $image['file'] }}"
                                data-prodi="{{ $image['prodi'] }}"
                                aria-label="{{ $image['label'] }}"
                            >
                                <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                    <button type="button" class="wisuda-thumb-nav" id="wisudaThumbNext" aria-label="Thumbnail berikutnya">›</button>
                </div>

                <p class="wisuda-gallery-caption" id="wisudaGalleryCaption">{{ $defaultImage['label'] ?? 'Baju Toga Mahasiswa' }}</p>
            </div>

            {{-- RIGHT: Product info --}}
            <div class="wisuda-pdp-info">
                <div class="wisuda-pdp-badges">
                    <span class="wisuda-chip">Resmi Poltekkes Kemenkes Banten</span>
                    <span class="wisuda-chip wisuda-chip-alt">Wisuda {{ $config['event_year'] }}</span>
                </div>

                <h1 class="wisuda-pdp-title">Pemesanan Toga Wisuda Poltekkes Kemenkes Banten</h1>
                <p class="wisuda-pdp-subtitle">
                    Toga wisuda resmi mahasiswa Poltekkes Kemenkes Banten Tahun {{ $config['event_year'] }}
                </p>

                <div class="wisuda-pdp-meta">
                    <div><span>Pelaksanaan</span><strong>{{ $config['event_date'] }}</strong></div>
                    <div><span>Lokasi</span><strong>{{ $config['event_location'] }}</strong></div>
                    <div><span>Pemesanan</span><strong>Minimal 10 hari sebelum wisuda</strong></div>
                </div>

                <div class="wisuda-pdp-package-brief">
                    <strong>Isi paket singkat:</strong>
                    Jubah/toga, topi segi lima, slebber/kerah, serta kalung/samir sesuai program studi.
                </div>

                <div class="wisuda-option-block">
                    <div class="wisuda-option-label">
                        <span>Program Studi</span>
                        <small id="wisudaProdiNote">Pilih prodi untuk melihat preview atribut resmi.</small>
                    </div>
                    <div class="wisuda-variant-row" id="wisudaProdiVariants">
                        @foreach($variants as $key => $variant)
                            <button
                                type="button"
                                class="wisuda-variant-btn"
                                data-prodi="{{ $key }}"
                                data-primary="{{ $imgBase.'/'.$variant['primary'] }}"
                                data-secondary="{{ $imgBase.'/'.$variant['secondary'] }}"
                                data-note="{{ $variant['note'] }}"
                                data-label="{{ $variant['label'] }}"
                            >
                                {{ $variant['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="wisuda-option-block">
                    <div class="wisuda-option-label">
                        <span>Ukuran Toga</span>
                        <a href="{{ route('wisuda.order.create') }}#size_note" class="wisuda-size-guide">Catatan ukuran custom</a>
                    </div>
                    <div class="wisuda-size-row">
                        @foreach(['S','M','L','XL','XXL','Custom'] as $size)
                            <span class="wisuda-size-pill">{{ $size }}</span>
                        @endforeach
                    </div>
                    <p class="wisuda-note" style="margin-top:0.5rem;">Ukuran dipilih saat checkout/form pemesanan.</p>
                </div>

                <div class="wisuda-cta-row">
                    <a href="{{ route('wisuda.order.create') }}" class="wisuda-cta-primary" id="wisudaOrderCta">
                        Pesan Baju Toga Sekarang
                    </a>
                    <a href="#kontak-panitia" class="wisuda-cta-secondary" title="Hubungi panitia">Kontak</a>
                </div>

                <div class="wisuda-seller-bar">
                    <div class="wisuda-seller-info">
                        <strong>Panitia Wisuda / HUMAS</strong>
                        <span>{{ $config['contact_name'] }}</span>
                        <span>Pelayanan {{ $config['service_hours'] }}</span>
                    </div>
                    <div class="wisuda-seller-actions">
                        <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-green">Hubungi Dias Arliadini</a>
                        <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-blue">Hubungi Kontak Alternatif</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wisuda-section" id="preview-prodi">
    <div class="container">
        <h2 class="wisuda-section-title">Preview Atribut per Program Studi</h2>
        <div class="wisuda-official-preview-grid">
            @foreach($variants as $key => $variant)
                <article class="wisuda-official-preview-card">
                    <h3>{{ $variant['label'] }}</h3>
                    <img
                        src="{{ $imgBase.'/'.$variant['primary'] }}"
                        alt="Kerah dan Kalung Wisuda {{ $variant['label'] }}"
                        loading="lazy"
                    >
                    <img
                        src="{{ $imgBase.'/'.$variant['secondary'] }}"
                        alt="Kalung Wisuda {{ $variant['label'] }}"
                        loading="lazy"
                    >
                    <p>{{ $variant['note'] }}</p>
                    <button
                        type="button"
                        class="btn-sm btn-teal wisuda-preview-select"
                        data-prodi="{{ $key }}"
                    >
                        Lihat di Galeri
                    </button>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="wisuda-section wisuda-section-alt" id="isi-paket">
    <div class="container">
        <h2 class="wisuda-section-title">Isi Paket Toga Wisuda</h2>
        <div class="wisuda-package-grid">
            <div class="wisuda-package-item">
                <img src="{{ $imgBase }}/Baju-Toga-Mahasiswa.jpeg" alt="Jubah/Toga Wisuda" loading="lazy">
                <span>Jubah/Toga Wisuda</span>
            </div>
            <div class="wisuda-package-item">
                <img src="{{ $imgBase }}/Topi-Toga-Segi-Lima.jpeg" alt="Topi Toga Segi Lima" loading="lazy">
                <span>Topi/Toga Cap Segi Lima</span>
            </div>
            <div class="wisuda-package-item">
                <img src="{{ $imgBase }}/sleber-kerah-wisuda.jpeg" alt="Slebber Kerah Wisuda" loading="lazy">
                <span>Slebber/Kerah Wisuda</span>
            </div>
            <div class="wisuda-package-item">
                <img src="{{ $imgBase }}/kerah-dan-kalung-wisuda-keperawatan.jpeg" alt="Kalung/Samir Prodi" loading="lazy">
                <span>Kalung/Samir sesuai prodi</span>
            </div>
            <div class="wisuda-package-item">
                <span>Atribut wisuda lain sesuai ketentuan Poltekkes Kemenkes Banten</span>
            </div>
        </div>
    </div>
</section>

<section class="wisuda-section" id="ketentuan-wisuda">
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

<section class="wisuda-section wisuda-section-alt" id="kontak-panitia">
    <div class="container">
        <h2 class="wisuda-section-title">Informasi Pemesanan dan Kontak Panitia</h2>
        <div class="wisuda-contact-card">
            <p>Bagi yang berminat melakukan order toga wisuda, silakan melakukan pemesanan melalui halaman ini.</p>
            <p>
                Bila ada pertanyaan silahkan hubungi panitia.
                Pelayanan jam 8 pagi hingga 17 WIB; di luar jam itu silahkan menunggu hari berikutnya.
            </p>
            <p>{{ $config['order_deadline_note'] }}</p>

            <div class="wisuda-contact-box">
                <strong>{{ $config['contact_name'] }}</strong>
                <p>{{ $config['contact_phone_primary'] }}</p>
                <p>atau {{ $config['contact_phone_secondary'] }}</p>
            </div>

            <div class="wisuda-hero-actions">
                <a href="{{ route('wisuda.order.create') }}" class="btn-sm btn-teal">Pesan Baju Toga Sekarang</a>
                <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-green">Hubungi Dias Arliadini</a>
                <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-blue">Hubungi Kontak Alternatif</a>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    const root = document.getElementById('wisudaProductPage');
    if (!root) return;

    const mainImage = document.getElementById('wisudaMainImage');
    const caption = document.getElementById('wisudaGalleryCaption');
    const thumbs = Array.from(document.querySelectorAll('.wisuda-thumb'));
    const track = document.getElementById('wisudaThumbTrack');
    const prevBtn = document.getElementById('wisudaThumbPrev');
    const nextBtn = document.getElementById('wisudaThumbNext');
    const prodiNote = document.getElementById('wisudaProdiNote');
    const orderCta = document.getElementById('wisudaOrderCta');
    const variantButtons = Array.from(document.querySelectorAll('.wisuda-variant-btn'));

    function setMainImage(src, alt, label) {
        if (!src) return;
        mainImage.src = src;
        mainImage.alt = alt || 'Paket Toga Wisuda';
        if (caption && label) caption.textContent = label;

        thumbs.forEach(function (thumb) {
            const active = thumb.getAttribute('data-src') === src;
            thumb.classList.toggle('is-active', active);
            if (active && track) {
                thumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
        });
    }

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            setMainImage(
                thumb.getAttribute('data-src'),
                thumb.getAttribute('data-alt'),
                thumb.getAttribute('aria-label')
            );
        });
    });

    if (prevBtn && track) {
        prevBtn.addEventListener('click', function () {
            track.scrollBy({ left: -140, behavior: 'smooth' });
        });
    }
    if (nextBtn && track) {
        nextBtn.addEventListener('click', function () {
            track.scrollBy({ left: 140, behavior: 'smooth' });
        });
    }

    function selectProdi(key) {
        const btn = variantButtons.find(function (item) {
            return item.getAttribute('data-prodi') === key;
        });
        if (!btn) return;

        variantButtons.forEach(function (item) {
            item.classList.toggle('is-active', item === btn);
        });

        const primary = btn.getAttribute('data-primary');
        const note = btn.getAttribute('data-note');
        const label = btn.getAttribute('data-label');

        if (prodiNote) prodiNote.textContent = note || '';
        setMainImage(primary, 'Preview atribut ' + label, 'Preview ' + label);

        if (orderCta) {
            const url = new URL(orderCta.href, window.location.origin);
            url.searchParams.set('prodi', key);
            orderCta.href = url.toString();
        }
    }

    variantButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            selectProdi(btn.getAttribute('data-prodi'));
        });
    });

    document.querySelectorAll('.wisuda-preview-select').forEach(function (btn) {
        btn.addEventListener('click', function () {
            selectProdi(btn.getAttribute('data-prodi'));
            root.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>
@endsection
