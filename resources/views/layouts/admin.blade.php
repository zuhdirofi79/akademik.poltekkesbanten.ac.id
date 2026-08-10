<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Layanan Akademik')</title>
    <link rel="stylesheet" href="{{ env('CSS_URL', asset('css/akademik.css')) }}">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-brand">Polkesten Admin</div>
            <p class="admin-sidebar-user">{{ auth()->user()->name }}<br><small>{{ auth()->user()->roleLabel() }}</small></p>
            <nav class="admin-sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.submissions.index') }}" class="{{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}">Pengajuan Layanan</a>
                <a href="{{ route('admin.queues.index') }}" class="{{ request()->routeIs('admin.queues.index') ? 'active' : '' }}">Antrean Layanan</a>
                <a href="{{ route('admin.queues.reports') }}" class="{{ request()->routeIs('admin.queues.reports*') ? 'active' : '' }}">Laporan Antrean</a>
                <a href="{{ route('admin.surveys.dashboard') }}" class="{{ request()->routeIs('admin.surveys.*') ? 'active' : '' }}">Survei Kepuasan</a>
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Laporan / Audit</a>
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.study-programs.index') }}" class="{{ request()->routeIs('admin.study-programs.*') ? 'active' : '' }}">Program Studi</a>
                    <a href="{{ route('admin.email-templates.index') }}" class="{{ request()->routeIs('admin.email-templates.*') ? 'active' : '' }}">Template Email</a>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Manajemen Admin</a>
                    <a href="{{ route('admin.test-email.index') }}" class="{{ request()->routeIs('admin.test-email.*') ? 'active' : '' }}">Test Email</a>
                    <a href="{{ route('admin.activity-logs.index') }}" class="{{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">Log Aktivitas</a>
                @elseif(auth()->user()->isAdminJurusan())
                    <a href="{{ route('admin.dashboard') }}#layanan-aktif">Daftar Layanan Aktif</a>
                @endif
                <a href="{{ route('admin.change-password.show') }}" class="{{ request()->routeIs('admin.change-password.*') ? 'active' : '' }}">Ganti Password</a>
                <a href="{{ route('beranda') }}" target="_blank">Lihat Website</a>
            </nav>
            <form method="POST" action="{{ route('admin.logout') }}" class="admin-sidebar-logout">
                @csrf
                <button type="submit" class="btn-sm btn-gray" style="width:100%;">Logout</button>
            </form>
        </aside>

        <main class="admin-main">
            @if(auth()->user()->must_change_password)
                <div class="alert alert-error">Password masih menggunakan password sementara. Segera ubah password.</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-error">{{ session('warning') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if(session('generated_password'))
                <div class="alert alert-info">
                    Password sementara untuk <strong>{{ session('generated_email') }}</strong>:
                    <code>{{ session('generated_password') }}</code>
                    <br>Simpan password ini sekali. Akun wajib ganti password saat login pertama.
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
