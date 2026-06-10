@extends('layouts.admin')

@section('title', 'Manajemen Pengajuan')

@section('content')
<style>
    @media print {
        .no-print, .admin-sidebar, .filter-bar, .btn-sm { display: none !important; }
        .admin-card { box-shadow: none; border: 1px solid #ccc; }
        body { font-size: 11pt; }
        .print-only { display: block !important; }
    }
</style>
<h1>Manajemen Pengajuan</h1>

<form method="GET" class="filter-bar admin-card no-print">
    <input type="text" name="search" class="form-control" placeholder="Cari tiket/NIM/nama/email/WA" value="{{ request('search') }}" style="max-width:220px;">
    <select name="service_type_id" class="form-select" style="max-width:220px;">
        <option value="">Semua Layanan</option>
        @foreach($serviceTypes as $type)
            <option value="{{ $type->id }}" @selected(request('service_type_id') == $type->id)>
                {{ $type->name }}{{ $type->is_active ? '' : ' (Legacy)' }}
            </option>
        @endforeach
    </select>
    <select name="status" class="form-select" style="max-width:180px;">
        <option value="">Semua Status</option>
        @foreach($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}</option>
        @endforeach
    </select>
    <select name="study_program_id" class="form-select" style="max-width:200px;">
        <option value="">Semua Prodi</option>
        @foreach($studyPrograms as $program)
            <option value="{{ $program->id }}" @selected(request('study_program_id') == $program->id)>{{ $program->name }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="max-width:160px;">
    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="max-width:160px;">
    <button type="submit" class="btn-sm btn-teal">Filter</button>
    <a href="{{ route('admin.submissions.export', request()->query()) }}" class="btn-sm btn-blue">Export CSV</a>
    <button type="button" class="btn-sm btn-gray" onclick="window.print()">Print Rekap</button>
</form>

<div class="admin-card" id="submissions-table">
    <h3 class="print-only" style="display:none;">Rekap Pengajuan Layanan Akademik — {{ now()->format('d/m/Y H:i') }}</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nomor Tiket</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Program Studi</th>
                <th>Jenis Layanan</th>
                <th>Email</th>
                <th>WhatsApp</th>
                <th>Status</th>
                <th class="no-print">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $submission)
                <tr>
                    <td>{{ $submission->ticket_number }}</td>
                    <td>{{ $submission->created_at->format('d/m/Y') }}</td>
                    <td>{{ $submission->full_name }}</td>
                    <td>{{ $submission->nim }}</td>
                    <td>{{ $submission->studyProgram->name }}</td>
                    <td>{{ $submission->serviceType->name }}</td>
                    <td>{{ $submission->email }}</td>
                    <td>{{ $submission->whatsapp }}</td>
                    <td><span class="status-badge">{{ $submission->status }}</span></td>
                    <td class="no-print"><a href="{{ route('admin.submissions.show', $submission) }}" class="btn-sm btn-teal">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="10">Belum ada pengajuan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="no-print">
        {{ $submissions->links() }}
    </div>
</div>

@endsection
