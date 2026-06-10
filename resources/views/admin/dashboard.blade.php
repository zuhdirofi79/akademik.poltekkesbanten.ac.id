@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<h1>Dashboard</h1>

<div class="stats-grid" style="margin:1.5rem 0;">
    <div class="stat-box">
        <h4>Total Pengajuan</h4>
        <div class="value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-box">
        <h4>Diajukan</h4>
        <div class="value">{{ $stats['by_status']['Diajukan'] }}</div>
    </div>
    <div class="stat-box">
        <h4>Diproses</h4>
        <div class="value">{{ $stats['by_status']['Diproses'] }}</div>
    </div>
    <div class="stat-box">
        <h4>Selesai / File Tersedia</h4>
        <div class="value">{{ $stats['by_status']['Selesai / File Tersedia'] }}</div>
    </div>
    <div class="stat-box">
        <h4>Terkirim Email</h4>
        <div class="value">{{ $stats['by_status']['Terkirim Email'] }}</div>
    </div>
    <div class="stat-box">
        <h4>Ditolak</h4>
        <div class="value">{{ $stats['by_status']['Ditolak'] }}</div>
    </div>
</div>

<div class="admin-card">
    <h3>Statistik per Jenis Layanan</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Total</th>
                <th>Tahun {{ now()->year }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stats['active_services'] as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->submissions_count }}</td>
                    <td>{{ $service->year_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
