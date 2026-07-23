@php
    $surveyUrl = route('survey.create');
@endphp
<section class="service-reminder-section" style="padding-top:0;">
    <div class="container">
        <div class="service-reminder-banner" style="background:#ecfdf5;border-color:#99f6e4;">
            <h2 class="service-reminder-title">Isi Survei Kepuasan</h2>
            <p class="service-reminder-text">
                Bantu kami meningkatkan kualitas layanan akademik dengan mengisi survei kepuasan layanan.
            </p>
            <div class="service-reminder-actions">
                <a href="{{ $surveyUrl }}" class="btn-sm btn-teal">Isi Survei Kepuasan</a>
            </div>
        </div>
    </div>
</section>
