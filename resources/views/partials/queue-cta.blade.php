@php
    $queueUrl = route('queues.index');
@endphp
<section class="service-reminder-section" style="padding-top:0;">
    <div class="container">
        <div class="service-reminder-banner" style="background:#eff6ff;border-color:#93c5fd;">
            <h2 class="service-reminder-title">Antrean Layanan Akademik</h2>
            <p class="service-reminder-text">
                Ambil nomor antrean untuk pelayanan langsung di ruang layanan akademik.
            </p>
            <div class="service-reminder-actions">
                <a href="{{ $queueUrl }}" class="btn-sm btn-blue">Ambil Nomor Antrean</a>
                <a href="{{ route('queues.display') }}" class="btn-sm btn-gray" target="_blank">Lihat Display</a>
            </div>
        </div>
    </div>
</section>
