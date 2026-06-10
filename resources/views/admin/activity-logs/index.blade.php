@extends('layouts.admin')

@section('title', 'Log Aktivitas Admin')

@section('content')
<h1>Log Aktivitas Admin</h1>

<form method="GET" class="filter-bar admin-card" style="margin-top:1rem;">
    <select name="action" class="form-select" style="max-width:220px;">
        <option value="">Semua Aksi</option>
        @foreach($actions as $action)
            <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="max-width:160px;">
    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="max-width:160px;">
    <button type="submit" class="btn-sm btn-teal">Filter</button>
</form>

<div class="admin-card">
    <table class="table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Admin</th>
                <th>Aksi</th>
                <th>Deskripsi</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? '-' }}<br><small>{{ $log->user->email ?? '' }}</small></td>
                    <td><code>{{ $log->action }}</code></td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada log aktivitas.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
