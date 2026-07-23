@php
    $query = request()->only(['date_from', 'date_to', 'service_type_id', 'satisfaction_level']);
@endphp
<form method="GET" action="{{ $action }}" class="filter-bar admin-card no-print">
    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}" style="max-width:160px;" title="Tanggal mulai">
    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}" style="max-width:160px;" title="Tanggal akhir">
    <select name="service_type_id" class="form-select" style="max-width:240px;">
        <option value="">Semua Layanan</option>
        @foreach($serviceTypes as $type)
            <option value="{{ $type->id }}" @selected(($filters['service_type_id'] ?? null) == $type->id)>{{ $type->name }}</option>
        @endforeach
    </select>
    <select name="satisfaction_level" class="form-select" style="max-width:180px;">
        <option value="">Semua Tingkat</option>
        <option value="puas" @selected(($filters['satisfaction_level'] ?? null) === 'puas')>PUAS</option>
        <option value="cukup_puas" @selected(($filters['satisfaction_level'] ?? null) === 'cukup_puas')>CUKUP PUAS</option>
        <option value="kurang_puas" @selected(($filters['satisfaction_level'] ?? null) === 'kurang_puas')>KURANG PUAS</option>
    </select>
    <button type="submit" class="btn-sm btn-teal">Terapkan Filter</button>
    <a href="{{ $action }}" class="btn-sm btn-gray">Reset Filter</a>
    @isset($extraActions)
        {!! $extraActions !!}
    @endisset
</form>
