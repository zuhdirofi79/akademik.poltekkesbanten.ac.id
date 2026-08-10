<x-mail::message>
# File Layanan Akademik Tersedia

Halo **{{ $submission->full_name }}**,

Pengajuan **{{ $submission->serviceType->name }}** dengan nomor tiket **{{ $submission->ticket_number }}** sudah selesai diproses.

Sebelum mengunduh file hasil layanan melalui sistem, mohon isi **Survei Kepuasan Layanan Akademik** terlebih dahulu.

<x-mail::button :url="$surveyUrl">
Isi Survei Kepuasan
</x-mail::button>

Setelah survei diisi, Anda dapat mengunduh file hasil melalui halaman Progres Layanan menggunakan NIM/email dan nomor tiket/token Anda.

@foreach($downloadLinks as $link)
- Link unduh (aktif setelah survei diisi): [{{ $link['name'] }}]({{ $link['url'] }})
@endforeach

<x-mail::button :url="$progressUrl">
Cek Progres Layanan
</x-mail::button>

---

Jika terdapat kendala, silakan hubungi admin layanan akademik melalui email:
[{{ config('akademik.admin_contact_email') }}](mailto:{{ config('akademik.admin_contact_email') }})

Terima kasih,<br>
Layanan Akademik Poltekkes Kemenkes Banten
</x-mail::message>
