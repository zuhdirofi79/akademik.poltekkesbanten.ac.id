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

Jika terdapat kendala, silakan hubungi admin layanan akademik melalui email:
[{{ config('akademik.admin_contact_email') }}](mailto:{{ config('akademik.admin_contact_email') }})

Terima kasih,<br>
Layanan Akademik Poltekkes Kemenkes Banten
</x-mail::message>
