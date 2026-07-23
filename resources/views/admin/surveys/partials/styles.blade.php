<style>
.survey-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.85rem;
    margin-bottom: 1rem;
}
.survey-stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.9rem 1rem;
}
.survey-stat-card .label { font-size: 0.8rem; color: #6b7280; margin-bottom: 0.25rem; }
.survey-stat-card .value { font-size: 1.55rem; font-weight: 700; color: #134e4a; line-height: 1.1; }
.survey-stat-card .sub { font-size: 0.8rem; color: #64748b; margin-top: 0.2rem; }
.survey-stat-card.is-puas .value { color: #16a34a; }
.survey-stat-card.is-cukup .value { color: #ca8a04; }
.survey-stat-card.is-kurang .value { color: #dc2626; }
.survey-chart-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}
.survey-chart-box {
    min-height: 280px;
    position: relative;
}
.survey-chart-box canvas { max-height: 300px; }
.survey-badge-puas,
.survey-badge-cukup,
.survey-badge-kurang,
.survey-badge-default {
    display: inline-block;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #fff;
}
.survey-badge-puas { background: #16a34a; }
.survey-badge-cukup { background: #ca8a04; color: #111; }
.survey-badge-kurang { background: #dc2626; }
.survey-badge-default { background: #64748b; }
.survey-css-bars { display: flex; flex-direction: column; gap: 0.55rem; }
.survey-css-bar-row {
    display: grid;
    grid-template-columns: minmax(120px, 220px) 1fr 48px;
    gap: 0.5rem;
    align-items: center;
}
.survey-css-bar-label { font-size: 0.85rem; color: #334155; }
.survey-css-bar-track {
    height: 14px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}
.survey-css-bar-fill { height: 100%; border-radius: 999px; }
.survey-css-bar-value { text-align: right; font-weight: 700; font-size: 0.85rem; }
.survey-print-only { display: none; }
.survey-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 0.75rem 0 1rem; }
@media (max-width: 900px) {
    .survey-chart-grid { grid-template-columns: 1fr; }
    .survey-css-bar-row { grid-template-columns: 1fr; }
}
@media print {
    .no-print, .admin-sidebar, .admin-sidebar-logout, .filter-bar { display: none !important; }
    .admin-shell { display: block !important; }
    .admin-main, .content { margin: 0 !important; width: 100% !important; padding: 0 !important; }
    body, .admin-body { background: #fff !important; }
    .admin-card { box-shadow: none !important; border: 1px solid #ccc !important; break-inside: avoid; }
    .survey-print-only { display: block !important; }
    .survey-chart-js { display: none !important; }
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
}
</style>
