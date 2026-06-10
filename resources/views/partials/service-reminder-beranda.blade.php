@php
    $adminEmail = config('akademik.admin_contact_email');
    $adminWhatsappUrl = config('akademik.admin_contact_whatsapp_url');
@endphp
<section class="service-reminder-section">
    <div class="container">
        <div class="service-reminder-banner">
            <h2 class="service-reminder-title">Sudah Mengajukan Layanan?</h2>
            <p class="service-reminder-text">
                Bagi mahasiswa aktif dan alumni yang sudah mengajukan layanan akademik, mohon untuk selalu mengecek Progres Layanan secara berkala dan memeriksa email yang digunakan saat pengajuan. File hasil layanan akan dikirimkan melalui email setelah proses selesai.
            </p>
            <p class="service-reminder-note">
                Jika email belum masuk, silakan cek folder Spam/Junk/Promotion atau hubungi admin layanan akademik.
            </p>
            <div class="service-reminder-actions">
                <a href="{{ route('progress.index') }}" class="btn-sm btn-teal">Cek Progres Layanan</a>
                <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-green">Hubungi Admin</a>
                <a href="mailto:{{ $adminEmail }}" class="service-reminder-email">{{ $adminEmail }}</a>
            </div>
        </div>
    </div>
</section>
