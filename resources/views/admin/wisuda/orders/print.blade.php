<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Pemesanan dan Pembayaran Toga Wisuda</title>
    <link rel="stylesheet" href="{{ env('CSS_URL', asset('css/akademik.css')) }}">
    <style>
        body { background:#fff; color:#111; padding:1.5rem; }
        .toolbar { margin-bottom:1rem; display:flex; gap:0.5rem; flex-wrap:wrap; }
        table { width:100%; border-collapse:collapse; font-size:12px; }
        th, td { border:1px solid #cbd5e1; padding:0.4rem 0.45rem; vertical-align:top; }
        th { background:#f1f5f9; }
        .summary { display:flex; flex-wrap:wrap; gap:1rem; margin:1rem 0; }
        @media print {
            .no-print, .admin-sidebar, .navbar, .sidebar, .toolbar { display:none !important; }
            body { padding:0; }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button type="button" class="btn-sm btn-teal" onclick="window.print()">Cetak / Save as PDF</button>
        <a href="{{ route('admin.wisuda.orders.index', request()->query()) }}" class="btn-sm btn-gray">Kembali</a>
    </div>

    <h1 style="margin-bottom:0.25rem;">Rekap Pemesanan dan Pembayaran Toga Wisuda</h1>
    <h2 style="margin-top:0;font-size:1.05rem;color:#334155;">Poltekkes Kemenkes Banten Tahun 2026/2027</h2>
    <p><strong>Periode laporan:</strong> {{ $periodLabel }}</p>
    <p><strong>Dicetak:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</p>

    <div class="summary">
        <div><strong>Total Pesanan:</strong> {{ $summary['total_orders'] }}</div>
        <div><strong>Total Bukti Masuk:</strong> {{ $summary['total_uploaded'] }}</div>
        <div><strong>Total Terverifikasi:</strong> {{ $summary['total_verified'] }}</div>
        <div><strong>Total Ditolak:</strong> {{ $summary['total_rejected'] }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Prodi</th>
                <th>Tahun Lulus</th>
                <th>HP/Telepon</th>
                <th>Ukuran</th>
                <th>Jml</th>
                <th>Status Pesanan</th>
                <th>Status Bayar</th>
                <th>Bukti</th>
                <th>Upload</th>
                <th>Verifikator</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->student_name }}</td>
                    <td>{{ $order->nim }}</td>
                    <td>{{ $order->programDisplayName() }}</td>
                    <td>{{ $order->graduation_year ?: '-' }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ $order->toga_size }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>{{ $order->statusLabel() }}</td>
                    <td>{{ $order->paymentStatusLabel() }}</td>
                    <td>{{ $order->hasPaymentProof() ? 'Ada' : 'Tidak' }}</td>
                    <td>{{ $order->payment_uploaded_at?->format('d/m/Y H:i') ?: '-' }}</td>
                    <td>{{ $order->paymentVerifiedBy?->name ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="15">Tidak ada data untuk periode/filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
