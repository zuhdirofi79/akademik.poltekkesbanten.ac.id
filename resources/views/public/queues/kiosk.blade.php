<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Antrean Layanan Akademik</title>
    <link rel="stylesheet" href="{{ env('CSS_URL', asset('css/akademik.css')) }}">
    <style>
        body { background: #f0fdfa; }
        .kiosk-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .kiosk-card {
            width: 100%;
            max-width: 560px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.12);
            padding: 2rem 1.5rem;
            text-align: center;
        }
        .kiosk-card img { width: 72px; height: auto; margin-bottom: 0.75rem; }
        .kiosk-card h1 { margin: 0 0 0.35rem; font-size: 1.55rem; color: #0f766e; }
        .kiosk-card p { color: #475569; margin: 0 0 1.25rem; }
        .kiosk-card .form-group { text-align: left; }
        .kiosk-card .btn-primary {
            width: 100%;
            padding: 1.1rem;
            font-size: 1.2rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="kiosk-shell">
        <div class="kiosk-card">
            <img src="{{ asset('src/img/logo.png') }}" alt="Logo Poltekkes Kemenkes Banten">
            <h1>Antrean Layanan Akademik</h1>
            <p>{{ $todayLabel }}</p>

            @if(session('error'))
                <div class="alert alert-error" style="text-align:left;">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error" style="text-align:left;">
                    <ul style="margin:0;padding-left:1.1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('queues.take') }}" id="kioskForm">
                @csrf
                <input type="hidden" name="confirm" value="1">
                <input type="hidden" name="from_kiosk" value="1">
                <div class="form-group">
                    <label for="visitor_name">Nama Pengunjung <small>(opsional)</small></label>
                    <input type="text" name="visitor_name" id="visitor_name" class="form-control" value="{{ old('visitor_name') }}">
                </div>
                <div class="form-group">
                    <label for="visitor_type">Jenis Pengunjung</label>
                    <select name="visitor_type" id="visitor_type" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        @foreach($visitorTypes as $type)
                            <option value="{{ $type }}" @selected(old('visitor_type', 'Mahasiswa') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="purpose">Keperluan Layanan <small>(opsional)</small></label>
                    <select name="purpose" id="purpose" class="form-control">
                        <option value="">-- Pilih keperluan --</option>
                        @foreach($purposeOptions as $option)
                            <option value="{{ $option['value'] }}" @selected(old('purpose') === $option['value'])>{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Ambil Nomor Antrean</button>
            </form>
            <p style="margin-top:1rem;font-size:0.9rem;">
                <a href="{{ route('queues.index') }}">Mode normal</a> ·
                <a href="{{ route('queues.display') }}" target="_blank">Display</a>
            </p>
        </div>
    </div>
    <script>
        document.getElementById('kioskForm')?.addEventListener('submit', function (e) {
            if (!confirm('Ambil nomor antrean sekarang?')) e.preventDefault();
        });
    </script>
</body>
</html>
