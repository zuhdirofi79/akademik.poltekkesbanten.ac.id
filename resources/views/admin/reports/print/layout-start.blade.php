<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $printTitle ?? 'Laporan Pelayanan Akademik' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color:#000; margin:24px; background:#fff; }
        .print-container { width:100%; margin:0; padding:0; }
        .header { text-align:center; margin-bottom:16px; border-bottom:2px solid #000; padding-bottom:10px; }
        .header h1 { margin:0; font-size:1.15rem; letter-spacing:0.02em; }
        .header h2 { margin:0.25rem 0 0; font-size:1rem; }
        .header p { margin:0.2rem 0 0; font-size:0.9rem; }
        .meta { margin:12px 0; font-size:12px; }
        .summary { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin:12px 0; }
        .summary div { border:1px solid #000; padding:6px; }
        .summary strong { display:block; font-size:1.1rem; margin-top:4px; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        th, td { border:1px solid #000; padding:4px; text-align:left; vertical-align:top; }
        th { background:#f3f3f3; }
        tr { page-break-inside:avoid; }
        .toolbar { margin-bottom:16px; }
        .toolbar button { padding:8px 12px; border:0; border-radius:6px; background:#0f766e; color:#fff; cursor:pointer; }
        .footer { margin-top:20px; font-size:11px; border-top:1px solid #000; padding-top:8px; }
        @media print {
            .no-print, .toolbar, .admin-sidebar, .navbar, .sidebar { display:none !important; }
            body { background:#fff !important; color:#000 !important; margin:10mm; }
        }
    </style>
</head>
<body>
<div class="toolbar no-print">
    <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
</div>
<div class="print-container">
    <div class="header">
        <h1>POLTEKKES KEMENKES BANTEN</h1>
        <h2>LAPORAN PELAYANAN AKADEMIK</h2>
        <p>Unit Administrasi Akademik</p>
    </div>
    <div class="meta">
        <div><strong>{{ $printTitle ?? 'Laporan Pelayanan Akademik' }}</strong></div>
        <div>Periode: {{ $period_label }}</div>
        <div>Waktu cetak: {{ $generatedAt->format('d/m/Y H:i') }} WIB</div>
        <div>Dicetak oleh: {{ $printedBy ?? '-' }}</div>
    </div>
