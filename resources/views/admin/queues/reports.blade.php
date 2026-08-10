@extends('layouts.admin')

@section('title', 'Laporan Antrean Layanan')

@section('content')
<style>
    .queue-report-actions { display:flex; gap:0.5rem; flex-wrap:wrap; }
    .queue-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:0.75rem; align-items:end; }
    .queue-chart-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; margin:1rem 0; }
    .queue-chart-box { min-height:260px; }
    .css-bar-row { display:flex; align-items:center; gap:0.5rem; margin:0.35rem 0; font-size:0.9rem; }
    .css-bar-label { width:140px; flex-shrink:0; }
    .css-bar-track { flex:1; background:#e2e8f0; border-radius:999px; height:10px; overflow:hidden; }
    .css-bar-fill { height:100%; border-radius:999px; }
    @media (max-width: 900px) { .queue-chart-grid { grid-template-columns:1fr; } }
    @media print {
        .admin-sidebar, .no-print, .queue-report-actions { display:none !important; }
        .admin-shell { display:block; }
        .admin-main { padding:0; }
    }
</style>

<div class="no-print" style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:flex-start;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Laporan Antrean Layanan Akademik</h1>
        <p style="margin:0;color:#64748b;">Rekap harian, mingguan, bulanan untuk monitoring dan audit layanan.</p>
        <p style="margin:0.4rem 0 0;"><strong>Periode:</strong> {{ $period_label }}</p>
    </div>
    <div class="queue-report-actions">
        <a href="{{ route('admin.queues.index') }}" class="btn-sm btn-gray">Dashboard Antrean</a>
        <a href="{{ route('admin.queues.reports.export', request()->query()) }}" class="btn-sm btn-blue">Export CSV</a>
        <a href="{{ route('admin.queues.reports.print', request()->query()) }}" class="btn-sm btn-teal" target="_blank">Cetak Laporan</a>
    </div>
</div>

<div class="admin-card no-print" style="margin-top:1rem;">
    <form method="GET" action="{{ route('admin.queues.reports') }}">
        <div class="queue-filter-grid">
            <div class="form-group" style="margin:0;">
                <label>Periode cepat</label>
                <select name="period" class="form-control">
                    <option value="today" @selected(($filters['period'] ?? '') === 'today')>Hari ini</option>
                    <option value="week" @selected(($filters['period'] ?? '') === 'week')>Minggu ini</option>
                    <option value="month" @selected(($filters['period'] ?? '') === 'month')>Bulan ini</option>
                    <option value="custom" @selected(($filters['period'] ?? '') === 'custom')>Kustom</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Tanggal mulai</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Tanggal akhir</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua</option>
                    @foreach(['waiting'=>'Menunggu','called'=>'Dipanggil','serving'=>'Dilayani','served'=>'Selesai','skipped'=>'Dilewati','cancelled'=>'Dibatalkan'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Keperluan</label>
                <select name="purpose" class="form-control">
                    <option value="">Semua</option>
                    @foreach($purposes as $purpose)
                        <option value="{{ $purpose }}" @selected(($filters['purpose'] ?? '') === $purpose)>{{ $purpose }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Jenis Pengunjung</label>
                <select name="visitor_type" class="form-control">
                    <option value="">Semua</option>
                    @foreach(\App\Models\AcademicServiceQueue::VISITOR_TYPES as $type)
                        <option value="{{ $type }}" @selected(($filters['visitor_type'] ?? '') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Petugas</label>
                <select name="officer_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" @selected((string)($filters['officer_id'] ?? '') === (string)$officer->id)>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn-sm btn-teal">Terapkan Filter</button>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid" style="margin:1rem 0;">
    <div class="stat-box"><h4>Total Pengunjung</h4><div class="value">{{ $summary['total'] }}</div></div>
    <div class="stat-box"><h4>Selesai Dilayani</h4><div class="value">{{ $summary['served'] }}</div></div>
    <div class="stat-box"><h4>Menunggu</h4><div class="value">{{ $summary['waiting'] }}</div></div>
    <div class="stat-box"><h4>Dilewati</h4><div class="value">{{ $summary['skipped'] }}</div></div>
    <div class="stat-box"><h4>Dibatalkan</h4><div class="value">{{ $summary['cancelled'] }}</div></div>
    <div class="stat-box"><h4>Rata-rata Tunggu</h4><div class="value" style="font-size:1.2rem;">{{ $summary['avg_wait_label'] }}</div></div>
    <div class="stat-box"><h4>Rata-rata Layanan</h4><div class="value" style="font-size:1.2rem;">{{ $summary['avg_service_label'] }}</div></div>
</div>

<div class="queue-chart-grid">
    <div class="admin-card">
        <h3>Jumlah Pengunjung per Hari</h3>
        <div class="queue-chart-box no-print"><canvas id="chartByDay"></canvas></div>
        @foreach($charts['by_day'] as $row)
            @php $max = max(1, $charts['by_day']->max('total')); @endphp
            <div class="css-bar-row">
                <div class="css-bar-label">{{ $row['label'] }}</div>
                <div class="css-bar-track"><div class="css-bar-fill" style="width:{{ ($row['total']/$max)*100 }}%;background:#0f766e;"></div></div>
                <strong>{{ $row['total'] }}</strong>
            </div>
        @endforeach
    </div>
    <div class="admin-card">
        <h3>Status Antrean</h3>
        <div class="queue-chart-box no-print"><canvas id="chartByStatus"></canvas></div>
        @foreach($charts['by_status'] as $row)
            @php $max = max(1, $charts['by_status']->max('total')); @endphp
            <div class="css-bar-row">
                <div class="css-bar-label">{{ $row['label'] }}</div>
                <div class="css-bar-track"><div class="css-bar-fill" style="width:{{ ($row['total']/$max)*100 }}%;background:#0369a1;"></div></div>
                <strong>{{ $row['total'] }}</strong>
            </div>
        @endforeach
    </div>
    <div class="admin-card">
        <h3>Keperluan Layanan Terbanyak</h3>
        <div class="queue-chart-box no-print"><canvas id="chartByPurpose"></canvas></div>
        @foreach($charts['by_purpose'] as $row)
            @php $max = max(1, $charts['by_purpose']->max('total') ?: 1); @endphp
            <div class="css-bar-row">
                <div class="css-bar-label">{{ \Illuminate\Support\Str::limit($row['label'], 18) }}</div>
                <div class="css-bar-track"><div class="css-bar-fill" style="width:{{ ($row['total']/$max)*100 }}%;background:#ca8a04;"></div></div>
                <strong>{{ $row['total'] }}</strong>
            </div>
        @endforeach
    </div>
    <div class="admin-card">
        <h3>Jumlah Pengunjung per Jam</h3>
        <div class="queue-chart-box no-print"><canvas id="chartByHour"></canvas></div>
        @foreach($charts['by_hour'] as $row)
            @php $max = max(1, $charts['by_hour']->max('total') ?: 1); @endphp
            <div class="css-bar-row">
                <div class="css-bar-label">{{ $row['hour'] }}</div>
                <div class="css-bar-track"><div class="css-bar-fill" style="width:{{ ($row['total']/$max)*100 }}%;background:#7c3aed;"></div></div>
                <strong>{{ $row['total'] }}</strong>
            </div>
        @endforeach
    </div>
</div>

<div class="admin-card">
    <h3>Tabel Rekap Antrean</h3>
    <div style="overflow:auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nomor</th>
                    <th>Nama</th>
                    <th>Identitas</th>
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
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->queue_date->format('d/m/Y') }}</td>
                        <td><strong>{{ $row->queue_code }}</strong></td>
                        <td>{{ $row->visitor_name ?? '-' }}</td>
                        <td>{{ $row->visitor_identity ?? '-' }}</td>
                        <td>{{ $row->visitor_type ?? '-' }}</td>
                        <td>{{ $row->purposeDisplay() }}</td>
                        <td>{{ $row->statusLabel() }}</td>
                        <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('H:i') ?? '-' }}</td>
                        <td>{{ optional($row->called_at)?->timezone('Asia/Jakarta')->format('H:i') ?? '-' }}</td>
                        <td>{{ optional($row->served_at)?->timezone('Asia/Jakarta')->format('H:i') ?? '-' }}</td>
                        <td>{{ optional($row->finished_at)?->timezone('Asia/Jakarta')->format('H:i') ?? '-' }}</td>
                        <td>{{ $row->formatDuration($row->waitingSeconds()) }}</td>
                        <td>{{ $row->formatDuration($row->serviceSeconds()) }}</td>
                        <td>{{ $row->officerName() }}</td>
                        <td>{{ $row->admin_note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="16">Tidak ada data antrean pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const byDay = @json($charts['by_day']);
    const byStatus = @json($charts['by_status']);
    const byPurpose = @json($charts['by_purpose']);
    const byHour = @json($charts['by_hour']);

    function makeBar(id, labels, data, color) {
        const canvas = document.getElementById(id);
        if (!canvas || typeof Chart === 'undefined') return;
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{ data, backgroundColor: color, borderRadius: 6 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    makeBar('chartByDay', byDay.map(r => r.label), byDay.map(r => r.total), '#0f766e');
    makeBar('chartByStatus', byStatus.map(r => r.label), byStatus.map(r => r.total), '#0369a1');
    makeBar('chartByPurpose', byPurpose.map(r => r.label), byPurpose.map(r => r.total), '#ca8a04');
    makeBar('chartByHour', byHour.map(r => r.hour), byHour.map(r => r.total), '#7c3aed');
})();
</script>
@endsection
