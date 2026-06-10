<x-mail::message>
# Update Status Pengajuan

Halo **{{ $submission->full_name }}**,

Status pengajuan **{{ $submission->serviceType->name }}** ({{ $submission->ticket_number }}) telah diperbarui.

**Status Sebelumnya:** {{ $oldStatus }}

**Status Saat Ini:** {{ $submission->status }}

@if($submission->admin_note)
**Catatan Admin:** {{ $submission->admin_note }}
@endif

<x-mail::button :url="$progressUrl">
Cek Progres Layanan
</x-mail::button>

---

{{ config('akademik.admin_contact.help_message') }}

WhatsApp: [{{ config('akademik.admin_contact.whatsapp_label') }}]({{ config('akademik.admin_contact.whatsapp_url') }})

Email: [{{ config('akademik.admin_contact.email') }}](mailto:{{ config('akademik.admin_contact.email') }})

Terima kasih,<br>
Layanan Akademik Poltekkes Kemenkes Banten
</x-mail::message>
