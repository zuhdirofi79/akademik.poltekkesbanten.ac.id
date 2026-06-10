@php
    $adminEmail = config('akademik.admin_contact_email');
    $adminWhatsappUrl = config('akademik.admin_contact_whatsapp_url');
@endphp
<div class="service-reminder-alert alert alert-info">
    <h3 class="service-reminder-alert-title">Perhatian untuk Mahasiswa dan Alumni</h3>
    <p>
        Setelah mengajukan layanan akademik, silakan cek progres layanan secara berkala menggunakan nomor tiket/NIM dan email yang digunakan saat pengajuan. Jika pengajuan sudah selesai, file hasil layanan akan dikirimkan ke email tersebut.
    </p>
    <p>
        Jika email belum masuk, silakan cek folder Inbox, Spam, Junk, Promotion, atau hubungi admin layanan akademik.
    </p>
    <div class="service-reminder-contact">
        <p class="service-reminder-contact-label">
            Email Akademik:
            <a href="mailto:{{ $adminEmail }}" class="service-reminder-email-link">{{ $adminEmail }}</a>
        </p>
        <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-green">
            Hubungi Admin via WhatsApp
        </a>
    </div>
</div>
