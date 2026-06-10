@if(isset($navServices))
<section class="services-section" style="background:#fff;border-top:1px solid #e5e7eb;">
    <div class="container services-grid">
        @foreach($navServices as $service)
            <article class="service-card">
                <h3>{{ $service->name }}</h3>
                <p>{{ $service->description }}</p>
                <a href="{{ url('/layanan/' . $service->slug) }}">Buka Form →</a>
            </article>
        @endforeach
    </div>
</section>
@endif
