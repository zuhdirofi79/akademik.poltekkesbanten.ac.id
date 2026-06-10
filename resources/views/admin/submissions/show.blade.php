@extends('layouts.admin')

@section('title', 'Detail Pengajuan ' . $submission->ticket_number)

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <h1>Detail Pengajuan</h1>
    <a href="{{ route('admin.submissions.index') }}" class="btn-sm btn-gray">← Kembali</a>
</div>

<div class="admin-card">
    <h3>{{ $submission->ticket_number }} - {{ $submission->serviceType->name }}</h3>
    <p><span class="status-badge">{{ $submission->status }}</span></p>

    <table class="table">
        <tr><th>Nomor Tiket</th><td>{{ $submission->ticket_number }}</td></tr>
        <tr><th>Status</th><td>{{ $submission->status }}</td></tr>
        <tr><th>NIM</th><td>{{ $submission->nim }}</td></tr>
        <tr><th>Nama</th><td>{{ $submission->full_name }}</td></tr>
        <tr><th>NIK</th><td>{{ $submission->nik }}</td></tr>
        <tr><th>Program Studi</th><td>{{ $submission->studyProgram->name }}</td></tr>
        <tr><th>Email</th><td>{{ $submission->email }}</td></tr>
        <tr><th>WhatsApp</th><td>{{ $submission->whatsapp }}</td></tr>
        @if($submission->semester)<tr><th>Semester</th><td>{{ $submission->semester }}</td></tr>@endif
        @if($submission->mother_name)<tr><th>Nama Ibu Kandung</th><td>{{ $submission->mother_name }}</td></tr>@endif
        @if($submission->request_type)<tr><th>Jenis Permohonan</th><td>{{ $submission->request_type }}</td></tr>@endif
        @if($submission->corrected_data)<tr><th>Data Diperbaiki</th><td>{{ $submission->corrected_data }}</td></tr>@endif
        @if($submission->purpose)<tr><th>Keperluan</th><td>{{ $submission->purpose }}</td></tr>@endif
        @if($submission->issue_type)<tr><th>Kendala SIAKAD</th><td>{{ $submission->issue_type }}</td></tr>@endif
        @if($submission->leave_reason)<tr><th>Alasan Cuti</th><td>{{ $submission->leave_reason }}</td></tr>@endif
        @if($submission->entry_year)<tr><th>Tahun Masuk</th><td>{{ $submission->entry_year }}</td></tr>@endif
        @if($submission->graduation_year)<tr><th>Tahun Keluar</th><td>{{ $submission->graduation_year }}</td></tr>@endif
        @if($submission->alumni_letter_type)<tr><th>Jenis Surat Alumni (Legacy)</th><td>{{ $submission->alumni_letter_type }}</td></tr>@endif
        @if($submission->certificate_number)<tr><th>Nomor Ijazah</th><td>{{ $submission->certificate_number }}</td></tr>@endif
        <tr><th>Keterangan Tambahan</th><td>{{ $submission->additional_note }}</td></tr>
        <tr><th>Token Progres</th><td style="word-break:break-all;">{{ $submission->progress_token }}</td></tr>
        <tr><th>Tanggal Pengajuan</th><td>{{ $submission->created_at->format('d/m/Y H:i') }}</td></tr>
        @if($submission->admin_note)<tr><th>Catatan Admin</th><td>{{ $submission->admin_note }}</td></tr>@endif
    </table>
</div>

<div class="admin-card">
    <h3>File Pendukung Mahasiswa</h3>
    <ul class="download-list">
        @forelse($submission->applicantFiles as $file)
            <li>
                <a href="{{ route('download.file', ['submission' => $submission->id, 'file' => $file->id]) }}">
                    Unduh {{ $file->original_name }} ({{ \App\Enums\FileCategory::labelFor($file->file_category) }})
                </a>
            </li>
        @empty
            <li>Tidak ada file pendukung.</li>
        @endforelse
    </ul>
</div>

<div class="admin-card">
    <h3>Alur Offline (Microsoft Word)</h3>
    <p class="admin-instruction">
        Admin dapat mengunduh berkas pendukung mahasiswa, memproses dokumen secara offline menggunakan Microsoft Word atau aplikasi lain,
        lalu mengunggah file surat yang sudah jadi melalui form Upload File Final.
    </p>
</div>

<div class="admin-card">
    <h3>Ubah Status & Catatan Admin</h3>
    <form method="POST" action="{{ route('admin.submissions.status', $submission) }}">
        @csrf
        @method('PATCH')
        <div class="form-grid">
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-select" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected($submission->status === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full">
                <label for="admin_note_status">Catatan Admin</label>
                <textarea name="admin_note" id="admin_note_status" class="form-control" rows="3">{{ old('admin_note', $submission->admin_note) }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn-sm btn-blue">Simpan Status</button>
    </form>
</div>

<div class="admin-card">
    <h3>Upload File Final</h3>
    <form method="POST" action="{{ route('admin.submissions.upload-final', $submission) }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file_final">File Final / Surat Jadi</label>
            <input type="file" name="file_final" id="file_final" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
            <small>Format: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10 MB.</small>
        </div>
        <div class="form-group">
            <label for="catatan_admin">Catatan Admin (opsional)</label>
            <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="3">{{ old('catatan_admin', $submission->admin_note) }}</textarea>
        </div>
        <div class="form-group">
            <label style="font-weight:normal;">
                <input type="checkbox" name="ubah_status_otomatis" value="1" checked>
                Ubah status otomatis menjadi &ldquo;Selesai / File Tersedia&rdquo;
            </label>
        </div>
        <button type="submit" class="btn-sm btn-green">Upload File Final</button>
    </form>

    <h4 style="margin-top:1.5rem;">File Final Tersedia</h4>
    <ul class="download-list">
        @forelse($submission->finalFiles as $file)
            <li>
                <a href="{{ route('download.file', ['submission' => $submission->id, 'file' => $file->id]) }}">
                    Unduh {{ $file->original_name }}
                </a>
            </li>
        @empty
            <li>Belum ada file final.</li>
        @endforelse
    </ul>
</div>

<div class="admin-card">
    <h3>Notifikasi & Tautan Mahasiswa</h3>
    <form method="POST" action="{{ route('admin.submissions.send-email', $submission) }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn-sm btn-teal" @disabled(! $submission->hasFinalFiles())>Kirim Ulang Email File Final</button>
    </form>

    @if($whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn-sm btn-green" style="margin-left:0.5rem;">Buka WhatsApp Mahasiswa</a>
    @endif

    <div style="margin-top:1rem;">
        <label for="progress-link-copy" style="display:block;font-weight:600;margin-bottom:0.5rem;">Link Progres / Download Mahasiswa</label>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
            <input type="text" id="progress-link-copy" class="form-control" readonly value="{{ $progressPageUrl }}" style="max-width:420px;">
            <button type="button" class="btn-sm btn-gray" onclick="copyProgressInfo()">Salin Info Progres</button>
        </div>
        <small>Mahasiswa cek progres di halaman di atas dengan NIM/email + nomor tiket/token.</small>
    </div>

    @if($submission->email_sent_at)
        <p style="margin-top:1rem;color:#059669;">Email terakhir dikirim: {{ $submission->email_sent_at->format('d/m/Y H:i') }}</p>
    @endif
    @if($submission->email_error)
        <p style="color:#dc2626;">Error email: {{ $submission->email_error }}</p>
    @endif
    @if($submission->ready_to_send_email && ! $submission->email_sent_at)
        <p style="color:#0369a1;">Menunggu pengiriman email otomatis oleh cron job.</p>
    @endif
</div>

<div class="admin-card">
    <h3>Riwayat Status</h3>
    <table class="table">
        <thead>
            <tr><th>Tanggal</th><th>Dari</th><th>Ke</th><th>Catatan</th><th>Oleh</th></tr>
        </thead>
        <tbody>
            @foreach($submission->statusHistories as $history)
                <tr>
                    <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $history->old_status ?? '-' }}</td>
                    <td>{{ $history->new_status }}</td>
                    <td>{{ $history->note ?? '-' }}</td>
                    <td>{{ $history->changedByUser->name ?? 'Sistem' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
function copyProgressInfo() {
    const text = [
        'Halaman Progres: {{ $progressPageUrl }}',
        'Nomor Tiket: {{ $submission->ticket_number }}',
        'Token Progres: {{ $submission->progress_token }}',
        'NIM/Email: {{ $submission->nim }} / {{ $submission->email }}'
    ].join('\n');

    navigator.clipboard.writeText(text).then(function () {
        alert('Info progres berhasil disalin.');
    }).catch(function () {
        const input = document.getElementById('progress-link-copy');
        input.value = text;
        input.select();
        document.execCommand('copy');
        alert('Info progres berhasil disalin.');
    });
}
</script>
@endsection
