@extends('layouts.admin')

@section('title', 'Data Survei Kepuasan')

@section('content')
@include('admin.surveys.partials.styles')

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
    <div>
        <h1 style="margin-bottom:0.35rem;">Data Survei Kepuasan</h1>
        <p style="margin:0;color:#64748b;">Daftar jawaban survei mentah. Untuk grafik dan laporan audit, buka dashboard.</p>
        <p style="margin:0.4rem 0 0;font-size:0.9rem;"><strong>Periode:</strong> {{ $periodLabel }}</p>
    </div>
    <div class="survey-actions no-print">
        <a href="{{ route('admin.surveys.dashboard', request()->query()) }}" class="btn-sm btn-teal">Dashboard Grafik</a>
        <a href="{{ route('admin.surveys.export', request()->query()) }}" class="btn-sm btn-blue">Export CSV</a>
        <a href="{{ route('admin.surveys.print', request()->query()) }}" class="btn-sm btn-gray" target="_blank">Cetak Laporan Audit</a>
    </div>
</div>

<div class="survey-stat-grid">
    <div class="survey-stat-card">
        <div class="label">Total Responden</div>
        <div class="value">{{ $stats['total'] }}</div>
    </div>
    <div class="survey-stat-card is-puas">
        <div class="label">PUAS</div>
        <div class="value">{{ $stats['puas'] }}</div>
        <div class="sub">{{ $stats['pct_puas'] }}%</div>
    </div>
    <div class="survey-stat-card is-cukup">
        <div class="label">CUKUP PUAS</div>
        <div class="value">{{ $stats['cukup_puas'] }}</div>
        <div class="sub">{{ $stats['pct_cukup_puas'] }}%</div>
    </div>
    <div class="survey-stat-card is-kurang">
        <div class="label">KURANG PUAS</div>
        <div class="value">{{ $stats['kurang_puas'] }}</div>
        <div class="sub">{{ $stats['pct_kurang_puas'] }}%</div>
    </div>
</div>

@include('admin.surveys.partials.filters', [
    'action' => route('admin.surveys.index'),
    'filters' => $filters,
    'serviceTypes' => $serviceTypes,
])

<div class="admin-card">
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tingkat</th>
                <th>Layanan</th>
                <th>Alasan</th>
                <th>Saran/Masukan</th>
                <th>Responden</th>
                <th>Tiket</th>
            </tr>
        </thead>
        <tbody>
            @forelse($surveys as $survey)
                <tr>
                    <td>{{ $survey->created_at->format('d/m/Y H:i') }}</td>
                    <td><span class="{{ $survey->satisfactionBadgeClass() }}">{{ $survey->satisfactionLabel() }}</span></td>
                    <td>{{ $survey->serviceDisplayName() }}</td>
                    <td>{{ $survey->reason ?? '-' }}</td>
                    <td style="max-width:280px;white-space:pre-wrap;">{{ $survey->feedback ?? '-' }}</td>
                    <td>
                        {{ $survey->respondent_name ?? '-' }}
                        @if($survey->respondent_email)
                            <br><small>{{ $survey->respondent_email }}</small>
                        @endif
                    </td>
                    <td>{{ $survey->ticket_number ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada data survei.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="no-print">
        {{ $surveys->links() }}
    </div>
</div>
@endsection
