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

Jika terdapat kendala, silakan hubungi admin layanan akademik melalui email:
[{{ config('akademik.admin_contact_email') }}](mailto:{{ config('akademik.admin_contact_email') }})

Terima kasih,<br>
Layanan Akademik Poltekkes Kemenkes Banten
</x-mail::message>
