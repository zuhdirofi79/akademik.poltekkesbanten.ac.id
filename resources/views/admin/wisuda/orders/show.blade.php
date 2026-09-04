@extends('layouts.admin')

@section('title', 'Detail Pesanan '.$order->order_code)

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
    <h1 style="margin-bottom:0;">Detail Pesanan Toga</h1>
    <a href="{{ route('admin.wisuda.orders.index') }}" class="btn-sm btn-gray">Kembali</a>
</div>

<div class="admin-card" style="margin-top:1rem;">
    <h3 style="margin-top:0;">{{ $order->order_code }}</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:0.75rem 1.5rem;">
        <p><strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Nama:</strong> {{ $order->student_name }}</p>
        <p><strong>NIM:</strong> {{ $order->nim }}</p>
        <p><strong>NIK:</strong> {{ $order->nik ?: '-' }}</p>
        <p><strong>Program Studi:</strong> {{ $order->programDisplayName() }}</p>
        <p><strong>Tahun Lulus:</strong> {{ $order->graduation_year ?: '-' }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Nomor HP/Telepon:</strong> {{ $order->phone }}</p>
        <p><strong>Jenis Kelamin:</strong> {{ $order->genderLabel() }}</p>
        <p><strong>Ukuran Toga:</strong> {{ $order->toga_size }}</p>
        <p><strong>Catatan Ukuran:</strong> {{ $order->size_note ?: '-' }}</p>
        <p><strong>Jumlah:</strong> {{ $order->quantity }}</p>
        <p><strong>Status Pesanan:</strong> {{ $order->statusLabel() }}</p>
        <p><strong>Status Pembayaran:</strong> {{ $order->paymentStatusLabel() }}</p>
        <p><strong>Upload Bukti:</strong> {{ $order->payment_uploaded_at?->format('d/m/Y H:i') ?: '-' }}</p>
        <p><strong>Verifikasi:</strong> {{ $order->payment_verified_at?->format('d/m/Y H:i') ?: '-' }}</p>
        <p><strong>Verifikator:</strong> {{ $order->paymentVerifiedBy?->name ?: '-' }}</p>
        <p><strong>Penolakan:</strong> {{ $order->payment_rejected_at?->format('d/m/Y H:i') ?: '-' }}</p>
        <p><strong>Ditolak oleh:</strong> {{ $order->paymentRejectedBy?->name ?: '-' }}</p>
    </div>

    @if($order->admin_note)
        <p><strong>Catatan Admin:</strong> {{ $order->admin_note }}</p>
    @endif
    @if($order->rejection_note)
        <p><strong>Catatan Penolakan:</strong> {{ $order->rejection_note }}</p>
    @endif
</div>

<div class="admin-card">
    <h3 style="margin-top:0;">Bukti Pembayaran</h3>
    @if($order->hasPaymentProof())
        <p><strong>File:</strong> {{ $order->payment_proof_original_name }}</p>
        <p><strong>Ukuran:</strong> {{ number_format(($order->payment_proof_size ?? 0) / 1024, 1) }} KB</p>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1rem;">
            <a href="{{ route('admin.wisuda.orders.download-payment-proof', $order) }}" class="btn-sm btn-blue">Download Bukti</a>
        </div>
        @if($order->paymentProofIsImage())
            <img src="{{ route('admin.wisuda.orders.preview-payment-proof', $order) }}" alt="Preview bukti pembayaran" style="max-width:min(100%,480px);border:1px solid #e5e7eb;border-radius:8px;">
        @endif
    @else
        <p>Belum ada bukti pembayaran.</p>
    @endif
</div>

<div class="admin-card no-print">
    <h3 style="margin-top:0;">Aksi Admin</h3>
    <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:flex-start;">
        @if($order->hasPaymentProof() && $order->payment_status !== 'verified')
            <form method="POST" action="{{ route('admin.wisuda.orders.verify-payment', $order) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-sm btn-teal" onclick="return confirm('Verifikasi pembayaran pesanan ini?')">Verifikasi Pembayaran</button>
            </form>
        @endif

        @if($order->hasPaymentProof() && $order->payment_status !== 'rejected')
            <form method="POST" action="{{ route('admin.wisuda.orders.reject-payment', $order) }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                @csrf
                @method('PATCH')
                <input type="text" name="rejection_note" class="form-control" placeholder="Alasan penolakan wajib" required style="min-width:240px;">
                <button type="submit" class="btn-sm btn-gray" onclick="return confirm('Tolak pembayaran pesanan ini?')">Tolak Pembayaran</button>
            </form>
        @endif

        @if($order->status !== 'completed')
            <form method="POST" action="{{ route('admin.wisuda.orders.complete', $order) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-sm btn-blue" onclick="return confirm('Tandai pesanan selesai?')">Tandai Selesai</button>
            </form>
        @endif

        @if($order->status !== 'cancelled')
            <form method="POST" action="{{ route('admin.wisuda.orders.cancel', $order) }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                @csrf
                @method('PATCH')
                <input type="text" name="admin_note" class="form-control" placeholder="Catatan pembatalan (opsional)" style="min-width:220px;">
                <button type="submit" class="btn-sm btn-gray" onclick="return confirm('Batalkan pesanan ini?')">Batalkan Pesanan</button>
            </form>
        @endif
    </div>
</div>
@endsection
