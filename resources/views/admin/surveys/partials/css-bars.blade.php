@php
    $barRows = collect($rows)->values();
    $max = max(1, (int) $barRows->max('total'));
@endphp
<div class="survey-css-bars">
    @forelse($barRows as $row)
        @php
            $row = (array) $row;
            $label = $row['label'] ?? ($row['reason'] ?? ($row['service_name'] ?? '-'));
            $total = (int) ($row['total'] ?? 0);
            $width = round(($total / $max) * 100, 1);
            $color = $row['color'] ?? '#0f766e';
        @endphp
        <div class="survey-css-bar-row">
            <div class="survey-css-bar-label">{{ $label }}</div>
            <div class="survey-css-bar-track">
                <div class="survey-css-bar-fill" style="width:{{ $width }}%;background:{{ $color }};"></div>
            </div>
            <div class="survey-css-bar-value">{{ $total }}</div>
        </div>
    @empty
        <p style="margin:0;color:#6b7280;">Belum ada data untuk ditampilkan.</p>
    @endforelse
</div>
