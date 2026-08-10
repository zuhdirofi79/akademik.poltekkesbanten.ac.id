<style>
    .report-actions { display:flex; gap:0.5rem; flex-wrap:wrap; justify-content:flex-end; }
    .report-filter-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:0.75rem; align-items:end; }
    .report-chart-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; margin:1rem 0; }
    .report-chart-box { min-height:260px; }
    .report-table-wrap { overflow-x:auto; }
    .report-table-wrap table { min-width:1100px; font-size:0.85rem; }
    .report-subnav { display:flex; gap:0.5rem; flex-wrap:wrap; margin:0.75rem 0 1rem; }
    .report-subnav a { text-decoration:none; }
    @media (max-width: 900px) { .report-chart-grid { grid-template-columns:1fr; } }
    @media print {
        .admin-sidebar, .navbar, .sidebar, .no-print, .report-actions { display:none !important; }
        body { background:#fff !important; color:#000 !important; }
        .admin-shell { display:block; }
        .admin-main { padding:0; }
        .print-container { width:100%; margin:0; padding:0; }
        table { width:100%; border-collapse:collapse; font-size:11px; }
        th, td { border:1px solid #000; padding:4px; }
        tr { page-break-inside:avoid; }
    }
</style>
