@extends('layouts.admin')

@section('title', 'Laporan / Audit')

@section('content')
@include('admin.reports.partials.styles')

<div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:flex-start;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Laporan dan Audit Pelayanan Akademik</h1>
        <p style="margin:0;color:#64748b;">Rekap lengkap layanan akademik untuk kebutuhan monitoring, evaluasi, audit, dan pelaporan kepada pimpinan.</p>
        <p style="margin:0.4rem 0 0;"><strong>Periode:</strong> {{ $summary['period_label'] }}</p>
    </div>
    <div class="report-actions">
        <a href="{{ route('admin.reports.combined.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn-sm btn-blue">Download Laporan Gabungan Excel</a>
        <a href="{{ route('admin.reports.combined.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn-sm btn-gray">Download CSV Gabungan</a>
    </div>
</div>

@include('admin.reports.partials.subnav', ['active' => 'index'])

<div class="admin-card no-print" style="margin-bottom:1rem;">
    <form method="GET" action="{{ route('admin.reports.index') }}">
        <div class="report-filter-grid">
            @include('admin.reports.partials.period-filter')
            <div>
                <button type="submit" class="btn-sm btn-teal">Terapkan Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="btn-sm btn-gray">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="stats-grid" style="margin:1rem 0;">
    <div class="stat-box"><h4>Total Pengajuan Layanan</h4><div class="value">{{ $summary['cards']['total_submissions'] }}</div></div>
    <div class="stat-box"><h4>Total Pengajuan Selesai</h4><div class="value">{{ $summary['cards']['completed_submissions'] }}</div></div>
    <div class="stat-box"><h4>Total Pengajuan Belum Diproses</h4><div class="value">{{ $summary['cards']['pending_submissions'] }}</div></div>
    <div class="stat-box"><h4>Total File Final Terkirim Email</h4><div class="value">{{ $summary['cards']['email_sent'] }}</div></div>
    <div class="stat-box"><h4>Total Antrean Hari Ini</h4><div class="value">{{ $summary['cards']['queue_today'] }}</div></div>
    <div class="stat-box"><h4>Total Antrean Selesai</h4><div class="value">{{ $summary['cards']['queue_served'] }}</div></div>
    <div class="stat-box"><h4>Total Responden Survei Kepuasan</h4><div class="value">{{ $summary['cards']['survey_respondents'] }}</div></div>
    <div class="stat-box"><h4>Persentase Kepuasan PUAS</h4><div class="value">{{ $summary['cards']['survey_pct_puas'] }}%</div></div>
</div>

<div class="admin-card">
    <h3 style="margin-top:0;">Menu Laporan</h3>
    <div class="report-actions" style="justify-content:flex-start;">
        <a href="{{ route('admin.reports.submissions') }}" class="btn-sm btn-teal">Rekap Pengajuan Layanan</a>
        <a href="{{ route('admin.reports.queues') }}" class="btn-sm btn-teal">Rekap Antrean Layanan</a>
        <a href="{{ route('admin.reports.surveys') }}" class="btn-sm btn-teal">Rekap Survei Kepuasan</a>
        <a href="{{ route('admin.reports.combined') }}" class="btn-sm btn-blue">Laporan Gabungan</a>
    </div>
</div>

@if($exportLogs->isNotEmpty())
<div class="admin-card" style="margin-top:1rem;">
    <h3 style="margin-top:0;">Riwayat Export Terbaru</h3>
    <div class="report-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Admin</th>
                    <th>Jenis Laporan</th>
                    <th>Format</th>
                    <th>Nama File</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exportLogs as $log)
                    <tr>
                        <td>{{ optional($log->exported_at)?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user?->name ?? '-' }}</td>
                        <td>{{ $log->report_type }}</td>
                        <td>{{ strtoupper($log->format) }}</td>
                        <td>{{ $log->file_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
