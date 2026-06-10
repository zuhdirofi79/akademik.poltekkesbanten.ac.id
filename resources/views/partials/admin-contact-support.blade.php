@php
    $contact = config('akademik.admin_contact');
@endphp
<div class="admin-contact-support">
    <p>{{ $contact['help_message'] }}</p>
    <div class="admin-contact-actions">
        <a href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener" class="btn-sm btn-green">
            {{ $contact['whatsapp_label'] }}
        </a>
        <a href="mailto:{{ $contact['email'] }}" class="btn-sm btn-gray">
            {{ $contact['email'] }}
        </a>
    </div>
</div>
