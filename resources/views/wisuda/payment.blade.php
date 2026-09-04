@extends('layouts.app')

@section('title', 'Pembayaran Toga Wisuda - '.$order->order_code)

@section('content')
<div class="page-title-bar">Pembayaran Toga Wisuda</div>

<section class="container form-layout">
    <div class="form-card" style="grid-column: 1 / -1;">
        <div class="form-card-header">{{ $config['payment_title'] }}</div>
        <div class="form-card-body" style="display:block;">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin:0;padding-left:1.1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="wisuda-order-summary">
                <p><strong>Kode Pesanan:</strong> {{ $order->order_code }}</p>
                <p><strong>Nama Pemesan:</strong> {{ $order->student_name }}</p>
                <p><strong>Program Studi:</strong> {{ $order->programDisplayName() }}</p>
                <p><strong>Tahun Lulus:</strong> {{ $order->graduation_year ?: '-' }}</p>
                <p><strong>Ukuran Toga:</strong> {{ $order->toga_size }}</p>
                <p><strong>Jumlah:</strong> {{ $order->quantity }}</p>
                <p><strong>Status Pembayaran:</strong>
                    @if($order->payment_status === 'unpaid')
                        Menunggu informasi pembayaran
                    @else
                        {{ $order->paymentStatusLabel() }}
                    @endif
                </p>
                @if($order->payment_uploaded_at)
                    <p><strong>Tanggal Upload Bukti:</strong> {{ $order->payment_uploaded_at->format('d/m/Y H:i') }}</p>
                @endif
                @if($order->rejection_note)
                    <p><strong>Catatan Penolakan:</strong> {{ $order->rejection_note }}</p>
                @endif
            </div>

            <div class="wisuda-status-waiting">
                <strong>Untuk informasi rekening pembayaran, silakan hubungi Tim Sekretariat.</strong>
                <p style="margin:0.5rem 0 0;">
                    Setelah mendapat info rekening dan menyelesaikan pembayaran, unggah bukti di form bawah.
                </p>
            </div>

            <div class="wisuda-payment-box">
                <h3 style="margin-top:0;">Instruksi Pembayaran</h3>
                <p>{{ $config['payment_instruction'] }}</p>

                @if($config['payment_bank_name'] || $config['payment_account_name'] || $config['payment_account_number'])
                    <div class="wisuda-contact-box" style="margin-top:1rem;">
                        @if($config['payment_bank_name'])
                            <p><strong>Bank:</strong> {{ $config['payment_bank_name'] }}</p>
                        @endif
                        @if($config['payment_account_name'])
                            <p><strong>Atas Nama:</strong> {{ $config['payment_account_name'] }}</p>
                        @endif
                        @if($config['payment_account_number'])
                            <p><strong>Nomor Rekening:</strong> {{ $config['payment_account_number'] }}</p>
                        @endif
                    </div>
                @endif

                <div class="wisuda-hero-actions" style="margin-top:1rem;">
                    <a href="{{ $config['contact_wa_primary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-green">
                        Hubungi Tim Sekretariat
                    </a>
                    <a href="{{ $config['contact_wa_secondary'] }}" target="_blank" rel="noopener noreferrer" class="btn-sm btn-blue">
                        Kontak Alternatif
                    </a>
                </div>
            </div>

            @if($order->hasPaymentProof())
                <div class="alert alert-info" style="margin-top:1rem;">
                    Bukti pembayaran sudah diunggah: <strong>{{ $order->payment_proof_original_name }}</strong>
                    @if($order->payment_status === 'rejected')
                        — status ditolak, Anda dapat mengunggah ulang.
                    @elseif($order->payment_status === 'uploaded')
                        — menunggu verifikasi Tim Sekretariat.
                    @endif
                </div>
            @endif

            @if(! in_array($order->status, ['cancelled', 'completed'], true) && $order->payment_status !== 'verified')
                <form method="POST"
                      action="{{ route('wisuda.payment.upload', ['orderCode' => $order->order_code]) }}"
                      enctype="multipart/form-data"
                      style="margin-top:1.25rem;">
                    @csrf
                    <input type="hidden" name="token" value="{{ $order->public_token }}">
                    <div class="form-group">
                        <label for="payment_proof">Upload Bukti Pembayaran <span style="color:#dc2626;">*</span></label>
                        <input type="file" name="payment_proof" id="payment_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small style="color:#6b7280;">Format: PDF, JPG, JPEG, PNG. Maksimal 5 MB.</small>
                    </div>
                    <button type="submit" class="btn-primary">Unggah Bukti Pembayaran</button>
                </form>
            @endif

            <div class="wisuda-hero-actions" style="margin-top:1.25rem;">
                <a href="{{ route('wisuda.order.show', ['orderCode' => $order->order_code, 'token' => $order->public_token]) }}" class="btn-sm btn-gray">
                    Lihat Detail Pesanan
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
