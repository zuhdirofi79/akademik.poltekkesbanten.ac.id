<x-mail::message>
# File Layanan Akademik Tersedia

Halo **{{ $submission->full_name }}**,

Pengajuan **{{ $submission->serviceType->name }}** dengan nomor tiket **{{ $submission->ticket_number }}** sudah selesai.

Silakan unduh file hasil melalui link berikut:

@foreach($downloadLinks as $link)
- [{{ $link['name'] }}]({{ $link['url'] }})
@endforeach

Anda juga dapat mengunduh file melalui halaman progres layanan.

<x-mail::button :url="$progressUrl">
Cek Progres Layanan
</x-mail::button>

@if($submission->canDownloadResults())
File hasil juga dapat diunduh melalui halaman progres layanan menggunakan nomor tiket dan token progres Anda.
@endif

---

{{ config('akademik.admin_contact.help_message') }}

WhatsApp: [{{ config('akademik.admin_contact.whatsapp_label') }}]({{ config('akademik.admin_contact.whatsapp_url') }})

Email: [{{ config('akademik.admin_contact.email') }}](mailto:{{ config('akademik.admin_contact.email') }})

Terima kasih,<br>
Layanan Akademik Poltekkes Kemenkes Banten
</x-mail::message>
