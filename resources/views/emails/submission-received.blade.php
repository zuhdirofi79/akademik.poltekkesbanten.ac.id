<x-mail::message>
# Pengajuan Layanan Akademik Diterima

Halo **{{ $submission->full_name }}**,

Pengajuan **{{ $submission->serviceType->name }}** Anda telah kami terima.

**Nomor Tiket:** {{ $submission->ticket_number }}

**Token Progres:** {{ $submission->progress_token }}

Simpan nomor tiket dan token dengan aman. Gunakan keduanya untuk mengecek progres pengajuan Anda.

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
