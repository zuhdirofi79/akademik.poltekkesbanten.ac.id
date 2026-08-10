<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Antrean Layanan Akademik</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color:#0f172a; margin:24px; }
        h1 { margin:0 0 0.25rem; font-size:1.4rem; }
        .muted { color:#64748b; }
        .summary { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin:16px 0; }
        .summary div { border:1px solid #cbd5e1; border-radius:8px; padding:8px; }
        .summary strong { display:block; font-size:1.2rem; margin-top:4px; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        th, td { border:1px solid #cbd5e1; padding:5px 6px; text-align:left; vertical-align:top; }
        th { background:#f1f5f9; }
        .toolbar { margin-bottom:16px; }
        .toolbar button { padding:8px 12px; border:0; border-radius:6px; background:#0f766e; color:#fff; cursor:pointer; }
        .footer { margin-top:20px; font-size:12px; color:#475569; border-top:1px solid #cbd5e1; padding-top:10px; }
        @media print {
            .toolbar { display:none !important; }
            body { margin:10mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <h1>Laporan Antrean Layanan Akademik</h1>
    <p class="muted" style="margin:0;">Poltekkes Kemenkes Banten</p>
    <p style="margin:0.35rem 0 0;"><strong>Periode:</strong> {{ $period_label }}</p>
    <p class="muted" style="margin:0.25rem 0 0;">Dicetak: {{ $generatedAt->format('d/m/Y H:i') }} WIB</p>

    <div class="summary">
        <div>Total Pengunjung<strong>{{ $summary['total'] }}</strong></div>
        <div>Selesai Dilayani<strong>{{ $summary['served'] }}</strong></div>
        <div>Dilewati<strong>{{ $summary['skipped'] }}</strong></div>
        <div>Dibatalkan<strong>{{ $summary['cancelled'] }}</strong></div>
        <div>Menunggu<strong>{{ $summary['waiting'] }}</strong></div>
        <div>Rata-rata Tunggu<strong>{{ $summary['avg_wait_label'] }}</strong></div>
        <div>Rata-rata Layanan<strong>{{ $summary['avg_service_label'] }}</strong></div>
    </div>

    <h3>Rekap Status</h3>
    <table>
        <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach($charts['by_status'] as $row)
                <tr><td>{{ $row['label'] }}</td><td>{{ $row['total'] }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top:1rem;">Detail Antrean</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nomor</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Keperluan</th>
                <th>Status</th>
                <th>Ambil</th>
                <th>Dipanggil</th>
                <th>Selesai</th>
                <th>Tunggu</th>
                <th>Layanan</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->queue_date->format('d/m/Y') }}</td>
                    <td>{{ $row->queue_code }}</td>
                    <td>{{ $row->visitor_name ?? '-' }}</td>
                    <td>{{ $row->visitor_type ?? '-' }}</td>
                    <td>{{ $row->purposeDisplay() }}</td>
                    <td>{{ $row->statusLabel() }}</td>
                    <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('H:i') ?? '-' }}</td>
                    <td>{{ optional($row->called_at)?->timezone('Asia/Jakarta')->format('H:i') ?? '-' }}</td>
                    <td>{{ optional($row->finished_at)?->timezone('Asia/Jakarta')->format('H:i') ?? '-' }}</td>
                    <td>{{ $row->formatDuration($row->waitingSeconds()) }}</td>
                    <td>{{ $row->formatDuration($row->serviceSeconds()) }}</td>
                    <td>{{ $row->officerName() }}</td>
                </tr>
            @empty
                <tr><td colspan="13">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan dari Sistem Antrean Layanan Akademik Poltekkes Kemenkes Banten.
    </div>
</body>
</html>
