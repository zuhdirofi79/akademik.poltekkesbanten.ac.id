@extends('layouts.app')

@section('title', 'Detail Pesanan Toga - '.$order->order_code)

@section('content')
<div class="page-title-bar">Detail Pesanan Toga Wisuda</div>

<section class="container form-layout">
    <div class="form-card" style="grid-column: 1 / -1;">
        <div class="form-card-header">Kode Pesanan: {{ $order->order_code }}</div>
        <div class="form-card-body" style="display:block;">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="wisuda-order-summary">
                <p><strong>Nama:</strong> {{ $order->student_name }}</p>
                <p><strong>NIM:</strong> {{ $order->nim }}</p>
                <p><strong>Program Studi:</strong> {{ $order->programDisplayName() }}</p>
                <p><strong>Email:</strong> {{ $order->email }}</p>
                <p><strong>Nomor HP/Telepon:</strong> {{ $order->phone }}</p>
                <p><strong>Jenis Kelamin:</strong> {{ $order->genderLabel() }}</p>
                <p><strong>Ukuran Toga:</strong> {{ $order->toga_size }}</p>
                <p><strong>Catatan Ukuran:</strong> {{ $order->size_note ?: '-' }}</p>
                <p><strong>Jumlah:</strong> {{ $order->quantity }}</p>
                <p><strong>Status Pesanan:</strong> {{ $order->statusLabel() }}</p>
                <p><strong>Status Pembayaran:</strong> {{ $order->paymentStatusLabel() }}</p>
            </div>

            <div class="wisuda-hero-actions" style="margin-top:1.25rem;">
                <a href="{{ route('wisuda.payment', ['orderCode' => $order->order_code, 'token' => $order->public_token]) }}" class="btn-sm btn-teal">
                    Lanjut ke Pembayaran
                </a>
                <a href="{{ route('wisuda.index') }}" class="btn-sm btn-gray">Kembali ke Wisuda</a>
            </div>
        </div>
    </div>
</section>
@endsection
