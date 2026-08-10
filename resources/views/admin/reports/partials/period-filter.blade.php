@php
    $periodOptions = $options['period_options'] ?? [
        'today' => 'Hari ini',
        'yesterday' => 'Kemarin',
        'this_week' => 'Minggu ini',
        'this_month' => 'Bulan ini',
        'this_year' => 'Tahun ini',
        'custom' => 'Custom tanggal',
    ];
@endphp
<div class="form-group" style="margin:0;">
    <label>Periode</label>
    <select name="period" class="form-control">
        @foreach($periodOptions as $value => $label)
            <option value="{{ $value }}" @selected(($filters['period'] ?? 'this_month') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="form-group" style="margin:0;">
    <label>Tanggal Mulai</label>
    <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? $filters['date_from'] ?? '' }}">
</div>
<div class="form-group" style="margin:0;">
    <label>Tanggal Akhir</label>
    <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? $filters['date_to'] ?? '' }}">
</div>
