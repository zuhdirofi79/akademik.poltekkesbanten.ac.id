<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Antrean {{ $queue->queue_code }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }
        .toolbar {
            max-width: 360px;
            margin: 1rem auto;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        .toolbar button, .toolbar a {
            border: 0;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-print { background: #0f766e; color: #fff; }
        .btn-back { background: #e2e8f0; color: #0f172a; }
        .ticket {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto 2rem;
            background: #fff;
            border: 1px dashed #94a3b8;
            border-radius: 12px;
            padding: 16px 14px;
            text-align: center;
        }
        .logo { width: 56px; height: auto; margin-bottom: 8px; }
        .title { font-size: 13px; font-weight: 700; margin: 0 0 4px; line-height: 1.35; }
        .subtitle { font-size: 11px; color: #475569; margin: 0 0 12px; }
        .code {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #0f766e;
            margin: 10px 0;
            line-height: 1;
        }
        .meta { font-size: 12px; margin: 4px 0; }
        .note {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
            font-size: 11px;
            color: #334155;
            line-height: 1.4;
        }
        @media print {
            body { background: #fff; }
            .toolbar, .no-print { display: none !important; }
            .ticket {
                width: 80mm;
                border: 0;
                margin: 0;
                border-radius: 0;
            }
            @page { margin: 4mm; size: auto; }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button type="button" class="btn-print" onclick="window.print()">Cetak</button>
        <a href="{{ route('queues.ticket', $queue) }}" class="btn-back">Kembali</a>
    </div>

    <div class="ticket">
        <img src="{{ asset('src/img/logo.png') }}" alt="Logo Poltekkes Kemenkes Banten" class="logo">
        <h1 class="title">Layanan Akademik<br>Poltekkes Kemenkes Banten</h1>
        <p class="subtitle">Nomor Antrean</p>
        <div class="code">{{ $queue->queue_code }}</div>
        <p class="meta"><strong>{{ $queue->queue_date->format('d/m/Y') }}</strong> · {{ $queue->created_at->timezone('Asia/Jakarta')->format('H:i') }}</p>
        @if($queue->visitor_name)
            <p class="meta">{{ $queue->visitor_name }}</p>
        @endif
        @if($queue->purposeDisplay() !== '-')
            <p class="meta">{{ $queue->purposeDisplay() }}</p>
        @endif
        <p class="note">Mohon menunggu hingga nomor antrean Anda dipanggil.</p>
    </div>

    <script>
        window.addEventListener('load', function () {
            const params = new URLSearchParams(window.location.search);
            if (params.get('autoprint') === '1') {
                setTimeout(() => window.print(), 400);
            }
        });
    </script>
</body>
</html>
