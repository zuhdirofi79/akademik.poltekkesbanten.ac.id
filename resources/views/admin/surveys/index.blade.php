@extends('layouts.admin')

@section('title', 'Survei Kepuasan')

@section('content')
<h1>Survei Kepuasan Layanan</h1>

<div class="admin-card">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;">
        <div><strong>Total</strong><div style="font-size:1.5rem;">{{ $stats['total'] }}</div></div>
        <div><strong>PUAS</strong><div style="font-size:1.5rem;color:#16a34a;">{{ $stats['puas'] }}</div></div>
        <div><strong>CUKUP PUAS</strong><div style="font-size:1.5rem;color:#ca8a04;">{{ $stats['cukup_puas'] }}</div></div>
        <div><strong>KURANG PUAS</strong><div style="font-size:1.5rem;color:#dc2626;">{{ $stats['kurang_puas'] }}</div></div>
    </div>
</div>

<form method="GET" class="filter-bar admin-card">
    <select name="satisfaction_level" class="form-select" style="max-width:180px;">
        <option value="">Semua Tingkat</option>
        <option value="puas" @selected(request('satisfaction_level') === 'puas')>PUAS</option>
        <option value="cukup_puas" @selected(request('satisfaction_level') === 'cukup_puas')>CUKUP PUAS</option>
        <option value="kurang_puas" @selected(request('satisfaction_level') === 'kurang_puas')>KURANG PUAS</option>
    </select>
    <select name="service_type_id" class="form-select" style="max-width:240px;">
        <option value="">Semua Layanan</option>
        @foreach($serviceTypes as $type)
            <option value="{{ $type->id }}" @selected(request('service_type_id') == $type->id)>{{ $type->name }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="max-width:160px;">
    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="max-width:160px;">
    <button type="submit" class="btn-sm btn-teal">Filter</button>
    <a href="{{ route('admin.surveys.export', request()->query()) }}" class="btn-sm btn-blue">Export CSV</a>
</form>

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
                    <td><span class="status-badge">{{ $survey->satisfactionLabel() }}</span></td>
                    <td>{{ $survey->serviceType->name ?? $survey->service_name ?? '-' }}</td>
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
    {{ $surveys->links() }}
</div>
@endsection
