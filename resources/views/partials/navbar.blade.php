<header class="site-header">
    <div class="container site-header-inner">
        <a href="{{ route('beranda') }}" class="site-brand">
            <img src="{{ asset('src/img/logo.png') }}" alt="Logo Poltekkes Kemenkes Banten" class="site-brand-logo">
            <span class="site-brand-text">Layanan Polkesten</span>
        </a>

        <nav class="site-nav">
            <a href="{{ route('beranda') }}" class="site-nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}">Home</a>

            <div class="site-nav-dropdown">
                <button type="button" class="site-nav-link site-nav-dropdown-toggle {{ request()->is('layanan/*') ? 'active' : '' }}">
                    Layanan Akademik
                    <span>▾</span>
                </button>
                <div class="site-nav-dropdown-menu">
                    @foreach($navServices as $service)
                        <a href="{{ url('/layanan/' . $service->slug) }}">{{ $service->name }}</a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('progress.index') }}" class="site-nav-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">Progres Layanan</a>
            <a href="{{ route('queues.index') }}" class="site-nav-link {{ request()->routeIs('queues.*') ? 'active' : '' }}">Antrean Layanan</a>
            <a href="{{ route('survey.create') }}" class="site-nav-link {{ request()->routeIs('survey.*') ? 'active' : '' }}">Survei Kepuasan</a>
            <a href="{{ route('about') }}" class="site-nav-link {{ request()->routeIs('about', 'tentang') ? 'active' : '' }}">Tentang</a>
            <a href="{{ route('wisuda.index') }}" class="site-nav-link {{ request()->routeIs('wisuda.*') ? 'active' : '' }}">Wisuda</a>
            <a href="https://poltekkesbanten.ac.id" target="_blank" rel="noopener" class="site-nav-button">POLTEKKES KEMENKES BANTEN</a>
            <a href="{{ route('admin.login') }}" class="site-admin-login">Login Admin</a>
        </nav>
    </div>
</header>
