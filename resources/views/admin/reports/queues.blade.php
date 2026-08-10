@extends('layouts.admin')

@section('title', 'Rekap Antrean Layanan')

@section('content')
@include('admin.reports.partials.styles')

<div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:flex-start;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Rekap Antrean Layanan Akademik</h1>
        <p style="margin:0;color:#64748b;">Rekap antrean untuk monitoring dan audit layanan tatap muka.</p>
        <p style="margin:0.4rem 0 0;"><strong>Periode:</strong> {{ $period_label }}</p>
    </div>
    <div class="report-actions no-print">
        <a href="{{ route('admin.reports.queues.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-sm btn-blue">Download Excel</a>
        <a href="{{ route('admin.reports.queues.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-sm btn-gray">Download CSV</a>
        <a href="{{ route('admin.reports.queues.print', request()->query()) }}" class="btn-sm btn-teal" target="_blank">Cetak Laporan</a>
    </div>
</div>

@include('admin.reports.partials.subnav', ['active' => 'queues'])

<div class="admin-card no-print">
    <form method="GET" action="{{ route('admin.reports.queues') }}">
        <div class="report-filter-grid">
            @include('admin.reports.partials.period-filter')
            <div class="form-group" style="margin:0;">
                <label>Nomor Antrean</label>
                <input type="text" name="queue_number" class="form-control" value="{{ $filters['queue_number'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Nama Pengunjung</label>
                <input type="text" name="visitor_name" class="form-control" value="{{ $filters['visitor_name'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Identitas Pengunjung</label>
                <input type="text" name="visitor_identity" class="form-control" value="{{ $filters['visitor_identity'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Jenis Pengunjung</label>
                <select name="visitor_type" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['visitor_types'] as $type)
                        <option value="{{ $type }}" @selected(($filters['visitor_type'] ?? '') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Keperluan Layanan</label>
                <select name="purpose" class="form-control">
                    <option value="">Semua</option>
                    @foreach($purposes as $purpose)
                        <option value="{{ $purpose }}" @selected(($filters['purpose'] ?? '') === $purpose)>{{ $purpose }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Status Antrean</label>
                <select name="status" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['queue_statuses'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Petugas</label>
                <select name="officer_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['officers'] as $officer)
                        <option value="{{ $officer->id }}" @selected((string)($filters['officer_id'] ?? '') === (string)$officer->id)>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Jam Antrean</label>
                <select name="hour" class="form-control">
                    <option value="">Semua</option>
                    @for($h = 7; $h <= 16; $h++)
                        <option value="{{ $h }}" @selected((string)($filters['hour'] ?? '') === (string)$h)>{{ sprintf('%02d:00', $h) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="btn-sm btn-teal">Terapkan Filter</button>
                <a href="{{ route('admin.reports.queues') }}" class="btn-sm btn-gray">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid" style="margin:1rem 0;">
    <div class="stat-box"><h4>Total Pengunjung</h4><div class="value">{{ $summary['total'] }}</div></div>
    <div class="stat-box"><h4>Menunggu</h4><div class="value">{{ $summary['waiting'] }}</div></div>
    <div class="stat-box"><h4>Dipanggil</h4><div class="value">{{ $summary['called'] }}</div></div>
    <div class="stat-box"><h4>Sedang Dilayani</h4><div class="value">{{ $summary['serving'] }}</div></div>
    <div class="stat-box"><h4>Selesai</h4><div class="value">{{ $summary['served'] }}</div></div>
    <div class="stat-box"><h4>Dilewati</h4><div class="value">{{ $summary['skipped'] }}</div></div>
    <div class="stat-box"><h4>Dibatalkan</h4><div class="value">{{ $summary['cancelled'] }}</div></div>
    <div class="stat-box"><h4>Rata-rata Tunggu</h4><div class="value">{{ $summary['avg_wait_label'] }}</div></div>
    <div class="stat-box"><h4>Rata-rata Layanan</h4><div class="value">{{ $summary['avg_service_label'] }}</div></div>
    <div class="stat-box"><h4>Jam Paling Ramai</h4><div class="value">{{ $summary['peak_hour'] }}</div></div>
    <div class="stat-box"><h4>Keperluan Terbanyak</h4><div class="value" style="font-size:1rem;">{{ $summary['top_purpose'] }}</div></div>
</div>

<div class="report-chart-grid no-print">
    <div class="admin-card report-chart-box"><canvas id="chartByDay"></canvas></div>
    <div class="admin-card report-chart-box"><canvas id="chartByStatus"></canvas></div>
    <div class="admin-card report-chart-box" style="grid-column:1/-1;"><canvas id="chartByHour"></canvas></div>
</div>

<div class="admin-card">
    <div class="report-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nomor</th>
                    <th>Nama</th>
                    <th>Identitas</th>
                    <th>HP</th>
                    <th>Jenis</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                    <th>Ambil</th>
                    <th>Dipanggil</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Tunggu</th>
                    <th>Layanan</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td>{{ $rows->firstItem() + $i }}</td>
                        <td>{{ optional($row->queue_date)->format('d/m/Y') }}</td>
                        <td>{{ $row->queue_code }}</td>
                        <td>{{ $row->visitor_name }}</td>
                        <td>{{ $row->visitor_identity }}</td>
                        <td>{{ $row->visitor_phone }}</td>
                        <td>{{ $row->visitor_type }}</td>
                        <td>{{ $row->purposeDisplay() }}</td>
                        <td>{{ $row->statusLabel() }}</td>
                        <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                        <td>{{ optional($row->called_at)?->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                        <td>{{ optional($row->served_at)?->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                        <td>{{ optional($row->finished_at)?->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                        <td>{{ $row->formatDuration($row->waitingSeconds()) }}</td>
                        <td>{{ $row->formatDuration($row->serviceSeconds()) }}</td>
                        <td>{{ $row->officerName() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="16">Tidak ada data untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="no-print" style="margin-top:1rem;">{{ $rows->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
try {
    const byDay = @json($charts['by_day']);
    const byStatus = @json($charts['by_status']);
    const byHour = @json($charts['by_hour']);
    new Chart(document.getElementById('chartByDay'), {
        type: 'bar',
        data: { labels: byDay.map(r => r.label), datasets: [{ label: 'Pengunjung per Hari', data: byDay.map(r => r.total), backgroundColor: '#2563eb' }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Jumlah Pengunjung per Hari' } } }
    });
    new Chart(document.getElementById('chartByStatus'), {
        type: 'doughnut',
        data: { labels: byStatus.map(r => r.label), datasets: [{ data: byStatus.map(r => r.total), backgroundColor: ['#0f766e','#2563eb','#f59e0b','#ef4444','#64748b','#14b8a6'] }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Status Antrean' } } }
    });
    new Chart(document.getElementById('chartByHour'), {
        type: 'bar',
        data: { labels: byHour.map(r => r.hour), datasets: [{ label: 'Jam Ramai', data: byHour.map(r => r.total), backgroundColor: '#0f766e' }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Jam Paling Ramai' } } }
    });
} catch (e) {}
</script>
@endsection
