@extends('layouts.admin')

@section('title', 'Pesanan Toga Wisuda')

@section('content')
<h1>Pesanan Toga Wisuda</h1>
<p style="color:#6b7280;margin-top:-0.5rem;">Rekap pemesanan dan pembayaran toga wisuda Poltekkes Kemenkes Banten Tahun 2026/2027</p>

<div class="admin-card" style="margin-bottom:1rem;">
    <div style="display:flex;flex-wrap:wrap;gap:1rem;">
        <div><strong>Total Pesanan:</strong> {{ $summary['total_orders'] }}</div>
        <div><strong>Bukti Masuk:</strong> {{ $summary['total_uploaded'] }}</div>
        <div><strong>Terverifikasi:</strong> {{ $summary['total_verified'] }}</div>
        <div><strong>Ditolak:</strong> {{ $summary['total_rejected'] }}</div>
    </div>
</div>

<form method="GET" class="filter-bar admin-card no-print" style="display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center;">
    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="max-width:150px;">
    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="max-width:150px;">
    <select name="period" class="form-select" style="max-width:150px;">
        <option value="">Periode cepat</option>
        <option value="today" @selected(request('period') === 'today')>Hari ini</option>
        <option value="week" @selected(request('period') === 'week')>Minggu ini</option>
        <option value="month" @selected(request('period') === 'month')>Bulan ini</option>
        <option value="year" @selected(request('period') === 'year')>Tahun ini</option>
    </select>
    <input type="text" name="order_code" class="form-control" placeholder="Kode pesanan" value="{{ request('order_code') }}" style="max-width:150px;">
    <input type="text" name="student_name" class="form-control" placeholder="Nama" value="{{ request('student_name') }}" style="max-width:150px;">
    <input type="text" name="nim" class="form-control" placeholder="NIM" value="{{ request('nim') }}" style="max-width:120px;">
    <input type="text" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}" style="max-width:160px;">
    <select name="study_program_id" class="form-select" style="max-width:180px;">
        <option value="">Semua Prodi</option>
        @foreach($studyPrograms as $program)
            <option value="{{ $program->id }}" @selected(request('study_program_id') == $program->id)>{{ $program->name }}</option>
        @endforeach
    </select>
    <select name="toga_size" class="form-select" style="max-width:120px;">
        <option value="">Ukuran</option>
        @foreach($togaSizes as $size)
            <option value="{{ $size }}" @selected(request('toga_size') === $size)>{{ $size }}</option>
        @endforeach
    </select>
    <select name="status" class="form-select" style="max-width:180px;">
        <option value="">Status Pesanan</option>
        @foreach($statuses as $key => $label)
            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="payment_status" class="form-select" style="max-width:170px;">
        <option value="">Status Pembayaran</option>
        @foreach($paymentStatuses as $key => $label)
            <option value="{{ $key }}" @selected(request('payment_status') === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="proof_filter" class="form-select" style="max-width:180px;">
        <option value="">Bukti Pembayaran</option>
        <option value="uploaded" @selected(request('proof_filter') === 'uploaded')>Sudah Upload</option>
        <option value="missing" @selected(request('proof_filter') === 'missing')>Belum Upload</option>
    </select>
    <button type="submit" class="btn-sm btn-teal">Filter</button>
    <a href="{{ route('admin.wisuda.orders.index') }}" class="btn-sm btn-gray">Reset</a>
    <a href="{{ route('admin.wisuda.orders.export', request()->query()) }}" class="btn-sm btn-blue">Download CSV/Excel</a>
    <a href="{{ route('admin.wisuda.orders.print', request()->query()) }}" class="btn-sm btn-teal" target="_blank">Cetak Rekap</a>
</form>

<div class="admin-card">
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pesan</th>
                <th>Kode Pesanan</th>
                <th>Nama Mahasiswa</th>
                <th>NIM</th>
                <th>Program Studi</th>
                <th>Email</th>
                <th>Nomor HP/Telepon</th>
                <th>Ukuran</th>
                <th>Jumlah</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
                <th>Bukti</th>
                <th>Upload Bukti</th>
                <th>Verifikator</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td>{{ $orders->firstItem() + $index }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->student_name }}</td>
                    <td>{{ $order->nim }}</td>
                    <td>{{ $order->programDisplayName() }}</td>
                    <td>{{ $order->email }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ $order->toga_size }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td><span class="status-badge">{{ $order->statusLabel() }}</span></td>
                    <td><span class="status-badge">{{ $order->paymentStatusLabel() }}</span></td>
                    <td>
                        @if($order->hasPaymentProof())
                            <a href="{{ route('admin.wisuda.orders.download-payment-proof', $order) }}" class="btn-sm btn-blue">Download</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $order->payment_uploaded_at?->format('d/m/Y H:i') ?: '-' }}</td>
                    <td>{{ $order->paymentVerifiedBy?->name ?: '-' }}</td>
                    <td><a href="{{ route('admin.wisuda.orders.show', $order) }}" class="btn-sm btn-teal">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="16">Belum ada pesanan toga wisuda.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="no-print">{{ $orders->links() }}</div>
</div>
@endsection
