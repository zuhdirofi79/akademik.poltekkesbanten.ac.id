@extends('layouts.admin')

@section('title', 'Rekap Survei Kepuasan')

@section('content')
@include('admin.reports.partials.styles')

<div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:flex-start;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Rekap Survei Kepuasan Layanan Akademik</h1>
        <p style="margin:0;color:#64748b;">Rekap kepuasan layanan untuk evaluasi dan audit mutu pelayanan.</p>
        <p style="margin:0.4rem 0 0;"><strong>Periode:</strong> {{ $period_label }}</p>
    </div>
    <div class="report-actions no-print">
        <a href="{{ route('admin.reports.surveys.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-sm btn-blue">Download Excel</a>
        <a href="{{ route('admin.reports.surveys.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-sm btn-gray">Download CSV</a>
        <a href="{{ route('admin.reports.surveys.print', request()->query()) }}" class="btn-sm btn-teal" target="_blank">Cetak Laporan</a>
    </div>
</div>

@include('admin.reports.partials.subnav', ['active' => 'surveys'])

<div class="admin-card no-print">
    <form method="GET" action="{{ route('admin.reports.surveys') }}">
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
                <label>Tingkat Kepuasan</label>
                <select name="satisfaction_level" class="form-control">
                    <option value="">Semua</option>
                    @foreach($options['satisfaction_levels'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['satisfaction_level'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label>Alasan</label>
                <input type="text" name="reason" class="form-control" value="{{ $filters['reason'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Nama Responden</label>
                <input type="text" name="respondent_name" class="form-control" value="{{ $filters['respondent_name'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Email Responden</label>
                <input type="text" name="respondent_email" class="form-control" value="{{ $filters['respondent_email'] ?? '' }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Nomor Tiket</label>
                <input type="text" name="ticket_number" class="form-control" value="{{ $filters['ticket_number'] ?? '' }}">
            </div>
            <div>
                <button type="submit" class="btn-sm btn-teal">Terapkan Filter</button>
                <a href="{{ route('admin.reports.surveys') }}" class="btn-sm btn-gray">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid" style="margin:1rem 0;">
    <div class="stat-box"><h4>Total Responden</h4><div class="value">{{ $summary['total'] }}</div></div>
    <div class="stat-box"><h4>Total PUAS</h4><div class="value">{{ $summary['puas'] }}</div></div>
    <div class="stat-box"><h4>Total CUKUP PUAS</h4><div class="value">{{ $summary['cukup_puas'] }}</div></div>
    <div class="stat-box"><h4>Total KURANG PUAS</h4><div class="value">{{ $summary['kurang_puas'] }}</div></div>
    <div class="stat-box"><h4>% PUAS</h4><div class="value">{{ $summary['pct_puas'] }}%</div></div>
    <div class="stat-box"><h4>% CUKUP PUAS</h4><div class="value">{{ $summary['pct_cukup_puas'] }}%</div></div>
    <div class="stat-box"><h4>% KURANG PUAS</h4><div class="value">{{ $summary['pct_kurang_puas'] }}%</div></div>
    <div class="stat-box"><h4>Layanan Terbanyak</h4><div class="value" style="font-size:1rem;">{{ $summary['top_service'] }}</div></div>
    <div class="stat-box"><h4>Kurang Puas Terbanyak</h4><div class="value" style="font-size:1rem;">{{ $summary['top_kurang_service'] }}</div></div>
</div>

<div class="report-chart-grid no-print">
    <div class="admin-card report-chart-box"><canvas id="chartByLevel"></canvas></div>
    <div class="admin-card report-chart-box"><canvas id="chartByReason"></canvas></div>
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
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Kepuasan</th>
                    <th>Alasan</th>
                    <th>Saran/Masukan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td>{{ $rows->firstItem() + $i }}</td>
                        <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                        <td>{{ $row->ticket_number ?? '-' }}</td>
                        <td>{{ $row->serviceDisplayName() }}</td>
                        <td>{{ $row->respondent_name ?? '-' }}</td>
                        <td>{{ $row->respondent_email ?? '-' }}</td>
                        <td>{{ $row->satisfactionLabel() }}</td>
                        <td>{{ $row->reason ?? '-' }}</td>
                        <td>{{ $row->feedback ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9">Tidak ada data untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="no-print" style="margin-top:1rem;">{{ $rows->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
try {
    const byLevel = @json($charts['by_level']);
    const byReason = @json($charts['by_reason']);
    new Chart(document.getElementById('chartByLevel'), {
        type: 'doughnut',
        data: { labels: byLevel.map(r => r.label), datasets: [{ data: byLevel.map(r => r.total), backgroundColor: ['#0f766e','#f59e0b','#ef4444'] }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Tingkat Kepuasan' } } }
    });
    new Chart(document.getElementById('chartByReason'), {
        type: 'bar',
        data: { labels: byReason.map(r => r.label), datasets: [{ label: 'Alasan', data: byReason.map(r => r.total), backgroundColor: '#2563eb' }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { title: { display: true, text: 'Alasan Kepuasan' } } }
    });
} catch (e) {}
</script>
@endsection
