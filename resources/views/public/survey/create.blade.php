@extends('layouts.app')

@section('title', 'Survei Kepuasan Layanan Akademik')

@section('content')
@php
    $oldLevel = old('satisfaction_level');
@endphp

<div class="page-title-bar">Survei Kepuasan Layanan Akademik</div>

<section class="container" style="padding:2rem 0 3rem;">
    <div class="form-card survey-card">
        <div class="form-card-header">Isi Survei Kepuasan</div>
        <div style="padding:1.5rem;">
            <p style="margin-top:0;">
                Bantu kami meningkatkan kualitas layanan akademik dengan mengisi survei kepuasan layanan.
            </p>

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin:0;padding-left:1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('survey.store') }}" id="survey-form">
                @csrf

                <div class="form-group full">
                    <label>Apakah Anda Puas dengan Layanan Kami?</label>
                    <div class="survey-level-grid">
                        @foreach([
                            'puas' => ['label' => 'PUAS', 'emoji' => '😊', 'class' => 'survey-level-puas'],
                            'cukup_puas' => ['label' => 'CUKUP PUAS', 'emoji' => '😐', 'class' => 'survey-level-cukup'],
                            'kurang_puas' => ['label' => 'KURANG PUAS', 'emoji' => '😞', 'class' => 'survey-level-kurang'],
                        ] as $value => $meta)
                            <label class="survey-level-option {{ $meta['class'] }}">
                                <input type="radio" name="satisfaction_level" value="{{ $value }}" @checked($oldLevel === $value) required>
                                <span class="survey-level-emoji" aria-hidden="true">{{ $meta['emoji'] }}</span>
                                <span class="survey-level-label">{{ $meta['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group full">
                    <label for="service_type_id">Pilih Jenis Layanan yang Anda nilai</label>
                    <select name="service_type_id" id="service_type_id" class="form-select">
                        <option value="">-- Pilih Layanan (opsional) --</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('service_type_id', $prefill['service_type_id'] ?? null) == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label for="reason">Alasan penilaian</label>
                    <select name="reason" id="reason" class="form-select">
                        <option value="">-- Pilih Alasan (opsional) --</option>
                    </select>
                    <small>Alasan menyesuaikan tingkat kepuasan yang dipilih.</small>
                </div>

                <div class="form-group full">
                    <label for="feedback">Saran, Masukan, atau Komentar</label>
                    <textarea name="feedback" id="feedback" class="form-control" rows="4" maxlength="2000" placeholder="Tuliskan saran atau masukan Anda (opsional)">{{ old('feedback') }}</textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="respondent_name">Nama Responden <span style="font-weight:normal;color:#6b7280;">(opsional)</span></label>
                        <input type="text" name="respondent_name" id="respondent_name" class="form-control" value="{{ old('respondent_name') }}">
                    </div>
                    <div class="form-group">
                        <label for="respondent_email">Email Responden <span style="font-weight:normal;color:#6b7280;">(opsional)</span></label>
                        <input type="email" name="respondent_email" id="respondent_email" class="form-control" value="{{ old('respondent_email') }}">
                    </div>
                    <div class="form-group full">
                        <label for="ticket_number">Nomor Tiket <span style="font-weight:normal;color:#6b7280;">(opsional)</span></label>
                        <input type="text" name="ticket_number" id="ticket_number" class="form-control" value="{{ old('ticket_number', $prefill['ticket_number'] ?? '') }}">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="margin-top:1rem;">Kirim Jawaban</button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    const reasonsByLevel = @json($reasonsByLevel);
    const reasonSelect = document.getElementById('reason');
    const oldReason = @json(old('reason'));

    function fillReasons(level) {
        const options = reasonsByLevel[level] || [];
        reasonSelect.innerHTML = '<option value="">-- Pilih Alasan (opsional) --</option>';
        options.forEach(function (reason) {
            const option = document.createElement('option');
            option.value = reason;
            option.textContent = reason;
            if (oldReason && oldReason === reason) {
                option.selected = true;
            }
            reasonSelect.appendChild(option);
        });
    }

    document.querySelectorAll('input[name="satisfaction_level"]').forEach(function (input) {
        input.addEventListener('change', function () {
            fillReasons(this.value);
        });
        if (input.checked) {
            fillReasons(input.value);
        }
    });
})();
</script>
<style>
.survey-level-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
}
.survey-level-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    border: 2px solid #d1d5db;
    border-radius: 12px;
    padding: 1rem 0.75rem;
    cursor: pointer;
    background: #fff;
    text-align: center;
    transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
}
.survey-level-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.survey-level-option:has(input:checked),
.survey-level-option:focus-within {
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
    transform: translateY(-1px);
}
.survey-level-emoji {
    font-size: 2.25rem;
    line-height: 1;
}
.survey-level-label {
    font-weight: 700;
    font-size: 0.9rem;
    color: #134e4a;
}
.survey-level-puas:has(input:checked) { border-color: #16a34a; }
.survey-level-cukup:has(input:checked) { border-color: #ca8a04; }
.survey-level-kurang:has(input:checked) { border-color: #dc2626; }
@media (max-width: 640px) {
    .survey-level-grid { grid-template-columns: 1fr; }
}
</style>
@endpush
