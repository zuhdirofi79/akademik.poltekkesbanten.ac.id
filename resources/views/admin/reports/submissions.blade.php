@extends('layouts.admin')

@section('title', 'Rekap Pengajuan Layanan')

@section('content')
@include('admin.reports.partials.styles')

<div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:flex-start;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Rekap Pengajuan Layanan Akademik</h1>
        <p style="margin:0;color:#64748b;">Data lengkap pengajuan untuk monitoring, evaluasi, dan audit.</p>
        <p style="margin:0.4rem 0 0;"><strong>Periode:</strong> {{ $period_label }}</p>
    </div>
    <div class="report-actions no-print">
        <a href="{{ route('admin.reports.submissions.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-sm btn-blue">Download Excel</a>
        <a href="{{ route('admin.reports.submissions.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-sm btn-gray">Download CSV</a>
        <a href="{{ route('admin.reports.submissions.print', request()->query()) }}" class="btn-sm btn-teal" target="_blank">Cetak Laporan</a>
    </div>
</div>

@include('admin.reports.partials.subnav', ['active' => 'submissions'])

<div class="admin-card no-print">
    <form method="GET" action="{{ route('admin.reports.submissions') }}">
        <div class="report-filter-grid">
            @include('admin.reports.partials.period-filter')
            <div class="form-group" style="margin:0;">
                <label>Nomor Tiket</label>
                <input type="text" name="ticket_number" class="form-control" value="{{ $filters['ticket_number'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Nama Pengaju</label>
                <input type="text" name="full_name" class="form-control" value="{{ $filters['full_name'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>NIM/NIP/NIK</label>
                <input type="text" name="identity" class="form-control" value="{{ $filters['identity'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="{{ $filters['email'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Nomor HP/Telepon</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ $filters['whatsapp'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Program Studi</label>
                <select name="study_program_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['study_programs'] as $prodi)
                        <option value="{{ $prodi->id }}" @selected((string)($filters['study_program_id'] ?? '') === (string)$prodi->id)>{{ $prodi->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Jenis Layanan</label>
                <select name="service_type_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['service_types'] as $service)
                        <option value="{{ $service->id }}" @selected((string)($filters['service_type_id'] ?? '') === (string)$service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['statuses'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Status Email Terkirim</label>
                <select name="email_sent" class="form-control">
                    <option value="">Semua</option>
                    <option value="yes" @selected(($filters['email_sent'] ?? '') === 'yes')>Ya</option>
                    <option value="no" @selected(($filters['email_sent'] ?? '') === 'no')>Tidak</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Status Upload File Final</label>
                <select name="has_final_file" class="form-control">
                    <option value="">Semua</option>
                    <option value="yes" @selected(($filters['has_final_file'] ?? '') === 'yes')>Ada</option>
                    <option value="no" @selected(($filters['has_final_file'] ?? '') === 'no')>Belum Ada</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Admin/Petugas Pemroses</label>
                <select name="officer_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['officers'] as $officer)
                        <option value="{{ $officer->id }}" @selected((string)($filters['officer_id'] ?? '') === (string)$officer->id)>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Verifikasi Jurusan</label>
                <select name="jurusan_verified" class="form-control">
                    <option value="">Semua</option>
                    <option value="yes" @selected(($filters['jurusan_verified'] ?? '') === 'yes')>Sudah</option>
                    <option value="no" @selected(($filters['jurusan_verified'] ?? '') === 'no')>Belum</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Verifikasi Pusat</label>
                <select name="central_verified" class="form-control">
                    <option value="">Semua</option>
                    <option value="yes" @selected(($filters['central_verified'] ?? '') === 'yes')>Sudah</option>
                    <option value="no" @selected(($filters['central_verified'] ?? '') === 'no')>Belum</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn-sm btn-teal">Terapkan Filter</button>
                <a href="{{ route('admin.reports.submissions') }}" class="btn-sm btn-gray">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid" style="margin:1rem 0;">
    <div class="stat-box"><h4>Total</h4><div class="value">{{ $summary['total'] }}</div></div>
    <div class="stat-box"><h4>Selesai</h4><div class="value">{{ $summary['completed'] }}</div></div>
    <div class="stat-box"><h4>Belum Diproses</h4><div class="value">{{ $summary['pending'] }}</div></div>
    <div class="stat-box"><h4>Email Terkirim</h4><div class="value">{{ $summary['email_sent'] }}</div></div>
    <div class="stat-box"><h4>Ada File Final</h4><div class="value">{{ $summary['with_final'] }}</div></div>
</div>

<div class="report-chart-grid no-print">
    <div class="admin-card report-chart-box"><canvas id="chartByService"></canvas></div>
    <div class="admin-card report-chart-box"><canvas id="chartByStatus"></canvas></div>
    <div class="admin-card report-chart-box" style="grid-column:1/-1;"><canvas id="chartByMonth"></canvas></div>
</div>

<div class="admin-card">
    <div class="report-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Tiket</th>
                    <th>Layanan</th>
                    <th>Status</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Prodi</th>
                    <th>Email</th>
                    <th>HP</th>
                    <th>File Final</th>
                    <th>Email Terkirim</th>
                    <th>Survei</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td>{{ $rows->firstItem() + $i }}</td>
                        <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                        <td><a href="{{ route('admin.submissions.show', $row) }}">{{ $row->ticket_number }}</a></td>
                        <td>{{ $row->serviceType?->name }}</td>
                        <td>{{ $row->status }}</td>
                        <td>{{ $row->full_name }}</td>
                        <td>{{ $row->nim }}</td>
                        <td>{{ $row->studyProgram?->name }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->whatsapp }}</td>
                        <td>{{ $row->finalFiles->isNotEmpty() ? 'Ada' : 'Tidak' }}</td>
                        <td>{{ $row->email_sent_at ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ $row->satisfactionSurvey ? 'Sudah' : 'Belum' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="13">Tidak ada data untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="no-print" style="margin-top:1rem;">{{ $rows->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
try {
    const byService = @json($charts['by_service']);
    const byStatus = @json($charts['by_status']);
    const byMonth = @json($charts['by_month']);
    new Chart(document.getElementById('chartByService'), {
        type: 'bar',
        data: { labels: byService.map(r => r.label), datasets: [{ label: 'Pengajuan per Layanan', data: byService.map(r => r.total), backgroundColor: '#0f766e' }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Jumlah Pengajuan per Layanan' } } }
    });
    new Chart(document.getElementById('chartByStatus'), {
        type: 'doughnut',
        data: { labels: byStatus.map(r => r.label), datasets: [{ data: byStatus.map(r => r.total), backgroundColor: ['#0f766e','#2563eb','#f59e0b','#ef4444','#64748b','#14b8a6','#8b5cf6','#06b6d4','#84cc16'] }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Status Pengajuan' } } }
    });
    new Chart(document.getElementById('chartByMonth'), {
        type: 'line',
        data: { labels: byMonth.map(r => r.label), datasets: [{ label: 'Pengajuan per Bulan', data: byMonth.map(r => r.total), borderColor: '#2563eb', tension: 0.25 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Pengajuan per Bulan' } } }
    });
} catch (e) {}
</script>
@endsection
