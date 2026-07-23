@extends('layouts.admin')

@section('title', 'Dashboard Survei Kepuasan')

@section('content')
@include('admin.surveys.partials.styles')

<div class="no-print" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Dashboard Survei Kepuasan Layanan Akademik</h1>
        <p style="margin:0;color:#64748b;max-width:720px;">
            Rekap hasil survei kepuasan layanan akademik sebagai bahan monitoring, evaluasi, dan audit layanan ADAK.
        </p>
        <p style="margin:0.4rem 0 0;font-size:0.9rem;"><strong>Periode:</strong> {{ $periodLabel }}</p>
    </div>
    <div class="survey-actions">
        <a href="{{ route('admin.surveys.index', request()->query()) }}" class="btn-sm btn-gray">Daftar Data</a>
        <a href="{{ route('admin.surveys.export', request()->query()) }}" class="btn-sm btn-blue">Export CSV</a>
        <a href="{{ route('admin.surveys.print', request()->query()) }}" class="btn-sm btn-teal" target="_blank">Cetak Laporan Audit</a>
    </div>
</div>

@include('admin.surveys.partials.filters', [
    'action' => route('admin.surveys.dashboard'),
    'filters' => $filters,
    'serviceTypes' => $serviceTypes,
])

<div class="survey-stat-grid">
    <div class="survey-stat-card">
        <div class="label">Total Responden</div>
        <div class="value">{{ $summary['total'] }}</div>
    </div>
    <div class="survey-stat-card is-puas">
        <div class="label">PUAS</div>
        <div class="value">{{ $summary['puas'] }}</div>
        <div class="sub">{{ $summary['pct_puas'] }}%</div>
    </div>
    <div class="survey-stat-card is-cukup">
        <div class="label">CUKUP PUAS</div>
        <div class="value">{{ $summary['cukup_puas'] }}</div>
        <div class="sub">{{ $summary['pct_cukup_puas'] }}%</div>
    </div>
    <div class="survey-stat-card is-kurang">
        <div class="label">KURANG PUAS</div>
        <div class="value">{{ $summary['kurang_puas'] }}</div>
        <div class="sub">{{ $summary['pct_kurang_puas'] }}%</div>
    </div>
    <div class="survey-stat-card is-puas">
        <div class="label">Persentase PUAS</div>
        <div class="value">{{ $summary['pct_puas'] }}%</div>
    </div>
    <div class="survey-stat-card is-cukup">
        <div class="label">Persentase CUKUP PUAS</div>
        <div class="value">{{ $summary['pct_cukup_puas'] }}%</div>
    </div>
    <div class="survey-stat-card is-kurang">
        <div class="label">Persentase KURANG PUAS</div>
        <div class="value">{{ $summary['pct_kurang_puas'] }}%</div>
    </div>
</div>

<div class="survey-chart-grid">
    <div class="admin-card">
        <h3>Distribusi Tingkat Kepuasan Layanan</h3>
        <div class="survey-chart-box survey-chart-js no-print">
            <canvas id="chartSatisfaction"></canvas>
        </div>
        <div style="margin-top:0.75rem;">
            @include('admin.surveys.partials.css-bars', [
                'rows' => [
                    ['label' => 'PUAS', 'total' => $summary['puas'], 'color' => '#16a34a'],
                    ['label' => 'CUKUP PUAS', 'total' => $summary['cukup_puas'], 'color' => '#ca8a04'],
                    ['label' => 'KURANG PUAS', 'total' => $summary['kurang_puas'], 'color' => '#dc2626'],
                ],
            ])
        </div>
    </div>

    <div class="admin-card">
        <h3>Jumlah Responden Berdasarkan Jenis Layanan</h3>
        <div class="survey-chart-box survey-chart-js no-print">
            <canvas id="chartServices"></canvas>
        </div>
        <div style="margin-top:0.75rem;">
            @include('admin.surveys.partials.css-bars', [
                'rows' => $byService->map(fn ($r) => ['label' => $r['service_name'], 'total' => $r['total'], 'color' => '#0f766e'])->all(),
            ])
        </div>
    </div>

    <div class="admin-card">
        <h3>Alasan Penilaian Responden</h3>
        <div class="survey-chart-box survey-chart-js no-print">
            <canvas id="chartReasons"></canvas>
        </div>
        <div style="margin-top:0.75rem;">
            @include('admin.surveys.partials.css-bars', [
                'rows' => collect($charts['by_reason_rows'])->map(fn ($r) => ['label' => $r['reason'], 'total' => $r['total'], 'color' => '#0369a1'])->all(),
            ])
        </div>
    </div>

    <div class="admin-card">
        <h3>Tren Respon Survei Bulanan</h3>
        <div class="survey-chart-box survey-chart-js no-print">
            <canvas id="chartMonthly"></canvas>
        </div>
        <div style="margin-top:0.75rem;">
            @include('admin.surveys.partials.css-bars', [
                'rows' => collect($charts['monthly_rows'])->map(fn ($r) => ['label' => $r['label'], 'total' => $r['total'], 'color' => '#7c3aed'])->all(),
            ])
        </div>
    </div>
</div>

<div class="admin-card" style="margin-top:1rem;">
    <h3>Tabel Rekap per Layanan</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nama Layanan</th>
                <th>Total Responden</th>
                <th>PUAS</th>
                <th>CUKUP PUAS</th>
                <th>KURANG PUAS</th>
                <th>% PUAS</th>
                <th>% CUKUP PUAS</th>
                <th>% KURANG PUAS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byService as $row)
                <tr>
                    <td>{{ $row['service_name'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['puas'] }}</td>
                    <td>{{ $row['cukup_puas'] }}</td>
                    <td>{{ $row['kurang_puas'] }}</td>
                    <td>{{ $row['pct_puas'] }}%</td>
                    <td>{{ $row['pct_cukup_puas'] }}%</td>
                    <td>{{ $row['pct_kurang_puas'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="8">Belum ada data rekap layanan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-card">
    <h3>Daftar Saran / Masukan Responden (50 terbaru)</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Layanan</th>
                <th>Tingkat Kepuasan</th>
                <th>Alasan</th>
                <th>Saran/Masukan</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Nomor Tiket</th>
            </tr>
        </thead>
        <tbody>
            @forelse($feedbackRows as $survey)
                <tr>
                    <td>{{ $survey->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $survey->serviceDisplayName() }}</td>
                    <td><span class="{{ $survey->satisfactionBadgeClass() }}">{{ $survey->satisfactionLabel() }}</span></td>
                    <td>{{ $survey->reason ?: '-' }}</td>
                    <td style="max-width:280px;white-space:pre-wrap;">{{ $survey->feedback ?: '-' }}</td>
                    <td>{{ $survey->respondent_name ?: '-' }}</td>
                    <td>{{ $survey->respondent_email ?: '-' }}</td>
                    <td>{{ $survey->ticket_number ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Belum ada saran/masukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') return;

    const charts = @json($charts);
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    };

    new Chart(document.getElementById('chartSatisfaction'), {
        type: 'doughnut',
        data: {
            labels: charts.satisfaction.labels,
            datasets: [{
                data: charts.satisfaction.values,
                backgroundColor: charts.satisfaction.colors
            }]
        },
        options: commonOptions
    });

    new Chart(document.getElementById('chartServices'), {
        type: 'bar',
        data: {
            labels: charts.services.labels,
            datasets: [{
                label: 'Jumlah Responden',
                data: charts.services.values,
                backgroundColor: '#0f766e'
            }]
        },
        options: {
            ...commonOptions,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    new Chart(document.getElementById('chartReasons'), {
        type: 'bar',
        data: {
            labels: charts.reasons.labels,
            datasets: [{
                label: 'Jumlah',
                data: charts.reasons.values,
                backgroundColor: '#0369a1'
            }]
        },
        options: {
            ...commonOptions,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    new Chart(document.getElementById('chartMonthly'), {
        type: 'line',
        data: {
            labels: charts.monthly.labels,
            datasets: [{
                label: 'Respon Survei',
                data: charts.monthly.values,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.15)',
                fill: true,
                tension: 0.25
            }]
        },
        options: {
            ...commonOptions,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
})();
</script>
@endsection
