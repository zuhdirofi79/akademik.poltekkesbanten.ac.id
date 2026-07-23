<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Survei Kepuasan Layanan Akademik</title>
    <link rel="stylesheet" href="{{ env('CSS_URL', asset('css/akademik.css')) }}">
    @include('admin.surveys.partials.styles')
    <style>
        body { background: #fff; color: #111; }
        .print-wrap { max-width: 1000px; margin: 0 auto; padding: 1.25rem; }
        .print-header { text-align: center; margin-bottom: 1.25rem; border-bottom: 2px solid #0f766e; padding-bottom: 0.85rem; }
        .print-header h1 { margin: 0 0 0.35rem; font-size: 1.35rem; color: #134e4a; }
        .print-header h2 { margin: 0; font-size: 1.05rem; font-weight: 600; }
        .print-header p { margin: 0.25rem 0 0; color: #475569; }
        .print-meta { margin: 0.75rem 0 1rem; font-size: 0.92rem; }
        .print-footer {
            margin-top: 1.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid #cbd5e1;
            font-size: 0.85rem;
            color: #475569;
            text-align: center;
        }
        .print-actions { margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap; }
        table.table th, table.table td { font-size: 0.85rem; }
        @media print {
            .no-print { display: none !important; }
            .print-wrap { max-width: 100%; padding: 0; }
            .admin-card { break-inside: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>
<div class="print-wrap">
    <div class="print-actions no-print">
        <button type="button" class="btn-sm btn-teal" onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="{{ route('admin.surveys.dashboard', request()->query()) }}" class="btn-sm btn-gray">Kembali ke Dashboard</a>
        <a href="{{ route('admin.surveys.export', request()->query()) }}" class="btn-sm btn-blue">Export CSV</a>
    </div>

    <header class="print-header">
        <h1>Laporan Survei Kepuasan Layanan Akademik</h1>
        <h2>Poltekkes Kemenkes Banten</h2>
        <p>Unit ADAK (Administrasi dan Akademik)</p>
    </header>

    <div class="print-meta">
        <p><strong>Periode laporan:</strong> {{ $periodLabel }}</p>
        <p><strong>Tanggal cetak:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</p>
        <p><strong>Dokumen:</strong> Bukti monitoring, evaluasi, dan audit layanan akademik</p>
    </div>

    <div class="survey-stat-grid">
        <div class="survey-stat-card">
            <div class="label">Total Responden</div>
            <div class="value">{{ $summary['total'] }}</div>
        </div>
        <div class="survey-stat-card is-puas">
            <div class="label">PUAS</div>
            <div class="value">{{ $summary['puas'] }}</div>
            <div class="sub">{{ $summary['pct_puas'] }}%</div>
        </div>
        <div class="survey-stat-card is-cukup">
            <div class="label">CUKUP PUAS</div>
            <div class="value">{{ $summary['cukup_puas'] }}</div>
            <div class="sub">{{ $summary['pct_cukup_puas'] }}%</div>
        </div>
        <div class="survey-stat-card is-kurang">
            <div class="label">KURANG PUAS</div>
            <div class="value">{{ $summary['kurang_puas'] }}</div>
            <div class="sub">{{ $summary['pct_kurang_puas'] }}%</div>
        </div>
    </div>

    <div class="admin-card">
        <h3>Distribusi Tingkat Kepuasan Layanan</h3>
        @include('admin.surveys.partials.css-bars', [
            'rows' => [
                ['label' => 'PUAS', 'total' => $summary['puas'], 'color' => '#16a34a'],
                ['label' => 'CUKUP PUAS', 'total' => $summary['cukup_puas'], 'color' => '#ca8a04'],
                ['label' => 'KURANG PUAS', 'total' => $summary['kurang_puas'], 'color' => '#dc2626'],
            ],
        ])
    </div>

    <div class="admin-card">
        <h3>Jumlah Responden Berdasarkan Jenis Layanan</h3>
        @include('admin.surveys.partials.css-bars', [
            'rows' => $byService->map(fn ($r) => ['label' => $r['service_name'], 'total' => $r['total'], 'color' => '#0f766e'])->all(),
        ])
    </div>

    <div class="admin-card">
        <h3>Alasan Penilaian Responden</h3>
        @include('admin.surveys.partials.css-bars', [
            'rows' => collect($charts['by_reason_rows'])->map(fn ($r) => ['label' => $r['reason'], 'total' => $r['total'], 'color' => '#0369a1'])->all(),
        ])
    </div>

    <div class="admin-card">
        <h3>Tren Respon Survei Bulanan</h3>
        @include('admin.surveys.partials.css-bars', [
            'rows' => collect($charts['monthly_rows'])->map(fn ($r) => ['label' => $r['label'], 'total' => $r['total'], 'color' => '#7c3aed'])->all(),
        ])
    </div>

    <div class="admin-card">
        <h3>Tabel Rekap per Layanan</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Layanan</th>
                    <th>Total</th>
                    <th>PUAS</th>
                    <th>CUKUP PUAS</th>
                    <th>KURANG PUAS</th>
                    <th>% PUAS</th>
                    <th>% CUKUP</th>
                    <th>% KURANG</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byService as $row)
                    <tr>
                        <td>{{ $row['service_name'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['puas'] }}</td>
                        <td>{{ $row['cukup_puas'] }}</td>
                        <td>{{ $row['kurang_puas'] }}</td>
                        <td>{{ $row['pct_puas'] }}%</td>
                        <td>{{ $row['pct_cukup_puas'] }}%</td>
                        <td>{{ $row['pct_kurang_puas'] }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="8">Belum ada data rekap layanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-card">
        <h3>Tabel Saran / Masukan Responden</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Layanan</th>
                    <th>Tingkat</th>
                    <th>Alasan</th>
                    <th>Saran/Masukan</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Tiket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbackRows as $survey)
                    <tr>
                        <td>{{ $survey->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $survey->serviceDisplayName() }}</td>
                        <td>{{ $survey->satisfactionLabel() }}</td>
                        <td>{{ $survey->reason ?: '-' }}</td>
                        <td style="white-space:pre-wrap;">{{ $survey->feedback ?: '-' }}</td>
                        <td>{{ $survey->respondent_name ?: '-' }}</td>
                        <td>{{ $survey->respondent_email ?: '-' }}</td>
                        <td>{{ $survey->ticket_number ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">Belum ada saran/masukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <footer class="print-footer">
        Dokumen ini dihasilkan dari Sistem Layanan Akademik Poltekkes Kemenkes Banten.
        <br>Dapat digunakan sebagai bukti monitoring, evaluasi, dan audit mutu layanan ADAK.
    </footer>
</div>
</body>
</html>
