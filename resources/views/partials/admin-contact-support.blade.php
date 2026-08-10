@php
    $adminEmail = config('akademik.admin_contact_email');
@endphp
<div class="admin-contact-support">
    <p>Jika terdapat kendala, hubungi admin layanan akademik melalui email:</p>
    <div class="admin-contact-actions">
        <a href="mailto:{{ $adminEmail }}" class="btn-sm btn-green">
            {{ $adminEmail }}
        </a>
    </div>
</div>
