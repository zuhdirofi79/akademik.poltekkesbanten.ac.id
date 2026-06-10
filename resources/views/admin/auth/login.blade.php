<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Layanan Akademik</title>
    <link rel="stylesheet" href="{{ env('CSS_URL', asset('css/akademik.css')) }}">
</head>
<body class="admin-body">
    <div class="login-card">
        <h2 style="margin-top:0;text-align:center;">Login Admin</h2>
        <p style="text-align:center;color:#6b7280;">Layanan Akademik Poltekkes Kemenkes Banten</p>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label style="font-weight:normal;color:#374151;">
                    <input type="checkbox" name="remember" value="1"> Ingat saya
                </label>
            </div>
            <button type="submit" class="btn-primary">Masuk</button>
        </form>

        <p style="text-align:center;margin-top:1rem;">
            <a href="{{ route('beranda') }}" style="color:#1aa0b8;">Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>
