@extends('layouts.admin')

@section('title', 'Laporan Gabungan')

@section('content')
@include('admin.reports.partials.styles')

<div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:flex-start;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Laporan Gabungan Pelayanan Akademik</h1>
        <p style="margin:0;color:#64748b;">Ringkasan lengkap untuk pimpinan/Direktur Poltekkes Kemenkes Banten.</p>
        <p style="margin:0.4rem 0 0;"><strong>Periode:</strong> {{ $period_label }}</p>
    </div>
    <div class="report-actions no-print">
        <a href="{{ route('admin.reports.combined.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-sm btn-blue">Download Excel</a>
        <a href="{{ route('admin.reports.combined.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-sm btn-gray">Download CSV</a>
        <a href="{{ route('admin.reports.combined.print', request()->query()) }}" class="btn-sm btn-teal" target="_blank">Cetak Laporan</a>
    </div>
</div>

@include('admin.reports.partials.subnav', ['active' => 'combined'])

<div class="admin-card no-print">
    <form method="GET" action="{{ route('admin.reports.combined') }}">
        <div class="report-filter-grid">
            @include('admin.reports.partials.period-filter')
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
                <label>Program Studi</label>
                <select name="study_program_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['study_programs'] as $prodi)
                        <option value="{{ $prodi->id }}" @selected((string)($filters['study_program_id'] ?? '') === (string)$prodi->id)>{{ $prodi->name }}</option>
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
            <div>
                <button type="submit" class="btn-sm btn-teal">Terapkan Filter</button>
                <a href="{{ route('admin.reports.combined') }}" class="btn-sm btn-gray">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid" style="margin:1rem 0;">
    <div class="stat-box"><h4>Total Pengajuan</h4><div class="value">{{ $submission_summary['total'] }}</div></div>
    <div class="stat-box"><h4>Pengajuan Selesai</h4><div class="value">{{ $submission_summary['completed'] }}</div></div>
    <div class="stat-box"><h4>Belum Selesai</h4><div class="value">{{ max(0, $submission_summary['total'] - $submission_summary['completed']) }}</div></div>
    <div class="stat-box"><h4>Email Terkirim</h4><div class="value">{{ $email_sent }}</div></div>
    <div class="stat-box"><h4>Email Belum Terkirim</h4><div class="value">{{ $email_not_sent }}</div></div>
    <div class="stat-box"><h4>Total Antrean</h4><div class="value">{{ $queue_summary['total'] }}</div></div>
    <div class="stat-box"><h4>Antrean Selesai</h4><div class="value">{{ $queue_summary['served'] }}</div></div>
    <div class="stat-box"><h4>Responden Survei</h4><div class="value">{{ $survey_summary['total'] }}</div></div>
    <div class="stat-box"><h4>% PUAS</h4><div class="value">{{ $survey_summary['pct_puas'] }}%</div></div>
</div>

<div class="report-chart-grid no-print">
    <div class="admin-card report-chart-box"><canvas id="chartTopServices"></canvas></div>
    <div class="admin-card report-chart-box"><canvas id="chartSubmissionStatus"></canvas></div>
</div>

<div class="admin-card">
    <h3 style="margin-top:0;">Layanan Paling Banyak Diajukan</h3>
    <ul>
        @forelse($top_services as $item)
            <li>{{ $item['label'] }} — <strong>{{ $item['total'] }}</strong></li>
        @empty
            <li>Tidak ada data.</li>
        @endforelse
    </ul>
</div>

<div class="admin-card" style="margin-top:1rem;">
    <h3 style="margin-top:0;">Catatan/Saran Penting dari Survei</h3>
    <div class="report-table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Layanan</th><th>Kepuasan</th><th>Saran/Masukan</th></tr></thead>
            <tbody>
                @forelse($important_feedback as $row)
                    <tr>
                        <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                        <td>{{ $row->serviceDisplayName() }}</td>
                        <td>{{ $row->satisfactionLabel() }}</td>
                        <td>{{ $row->feedback }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">Belum ada saran/masukan pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card" style="margin-top:1rem;">
    <h3 style="margin-top:0;">Pengajuan yang Masih Belum Selesai</h3>
    <div class="report-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Tanggal</th>
                    <th>Layanan</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($unfinished as $row)
                    <tr>
                        <td><a href="{{ route('admin.submissions.show', $row) }}">{{ $row->ticket_number }}</a></td>
                        <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                        <td>{{ $row->serviceType?->name }}</td>
                        <td>{{ $row->full_name }}</td>
                        <td>{{ $row->studyProgram?->name }}</td>
                        <td>{{ $row->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">Tidak ada pengajuan belum selesai pada filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
try {
    const topServices = @json($top_services);
    const statusChart = @json($submission_status_chart);
    new Chart(document.getElementById('chartTopServices'), {
        type: 'bar',
        data: { labels: topServices.map(r => r.label), datasets: [{ label: 'Layanan Terbanyak', data: topServices.map(r => r.total), backgroundColor: '#0f766e' }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Layanan Paling Banyak Diajukan' } } }
    });
    new Chart(document.getElementById('chartSubmissionStatus'), {
        type: 'doughnut',
        data: { labels: statusChart.map(r => r.label), datasets: [{ data: statusChart.map(r => r.total), backgroundColor: ['#0f766e','#2563eb','#f59e0b','#ef4444','#64748b','#14b8a6','#8b5cf6','#06b6d4','#84cc16'] }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Selesai vs Belum Selesai / Status' } } }
    });
} catch (e) {}
</script>
@endsection
