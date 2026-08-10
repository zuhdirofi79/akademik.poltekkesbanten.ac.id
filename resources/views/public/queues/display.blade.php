<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrean Layanan Akademik</title>
    <style>
        :root {
            --bg: #0b3b36;
            --panel: #0f4c45;
            --accent: #fbbf24;
            --text: #f8fafc;
            --muted: #cbd5e1;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(160deg, #072824 0%, #0f4c45 45%, #134e4a 100%);
            color: var(--text);
        }
        .shell { min-height: 100vh; padding: 1.25rem 1.5rem 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
        .top {
            display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;
        }
        .brand { display: flex; align-items: center; gap: 0.85rem; }
        .brand img { width: 56px; height: 56px; background: #fff; border-radius: 50%; padding: 4px; }
        .brand h1 { margin: 0; font-size: clamp(1.25rem, 2.5vw, 2rem); }
        .brand p { margin: 0.2rem 0 0; color: var(--muted); }
        .clock-box { text-align: right; }
        .clock-box .date { font-size: 1rem; color: var(--muted); }
        .clock-box .time { font-size: clamp(1.5rem, 3vw, 2.4rem); font-weight: 700; }
        .sound-toggle {
            border: 1px solid rgba(255,255,255,.35);
            background: rgba(255,255,255,.08);
            color: #fff;
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            cursor: pointer;
        }
        .main {
            flex: 1;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 1rem;
        }
        .panel {
            background: rgba(15, 76, 69, 0.85);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 1.25rem;
        }
        .current-label { margin: 0; color: var(--muted); letter-spacing: .04em; text-transform: uppercase; font-size: .95rem; }
        .current-code {
            margin: 0.5rem 0;
            font-size: clamp(4.5rem, 12vw, 9rem);
            font-weight: 800;
            color: var(--accent);
            line-height: 1;
            text-align: center;
        }
        .current-status {
            text-align: center;
            font-size: clamp(1.1rem, 2.2vw, 1.6rem);
            margin: 0.5rem 0 0;
        }
        .panel h2 { margin: 0 0 0.85rem; font-size: 1.2rem; }
        .queue-list { list-style: none; margin: 0; padding: 0; display: grid; gap: 0.55rem; }
        .queue-list li {
            background: rgba(255,255,255,.08);
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            display: flex;
            justify-content: space-between;
            font-size: 1.15rem;
            font-weight: 600;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }
        .stat {
            background: rgba(255,255,255,.08);
            border-radius: 12px;
            padding: 0.85rem;
            text-align: center;
        }
        .stat .label { color: var(--muted); font-size: .85rem; }
        .stat .value { font-size: 1.6rem; font-weight: 700; margin-top: 0.2rem; }
        .footer {
            text-align: center;
            color: var(--muted);
            font-size: .95rem;
        }
        @media (max-width: 900px) {
            .main, .stats { grid-template-columns: 1fr; }
            .clock-box { text-align: left; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="top">
            <div class="brand">
                <img src="{{ asset('src/img/logo.png') }}" alt="Logo Poltekkes">
                <div>
                    <h1>Antrean Layanan Akademik</h1>
                    <p>Poltekkes Kemenkes Banten</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                <button type="button" class="sound-toggle" id="soundToggle">Suara: ON</button>
                <div class="clock-box">
                    <div class="date" id="displayDate">{{ $initial['date'] }}</div>
                    <div class="time" id="displayTime">{{ $initial['time'] }}</div>
                </div>
            </div>
        </div>

        <div class="main">
            <div class="panel">
                <p class="current-label">Nomor Sedang Dipanggil</p>
                <div class="current-code" id="currentCode">{{ $initial['current']['queue_code'] ?? '—' }}</div>
                <p class="current-status" id="currentStatus">
                    {{ $initial['current'] ? 'Silakan menuju loket layanan akademik' : 'Menunggu pemanggilan antrean' }}
                </p>
            </div>
            <div class="panel">
                <h2>Antrean Menunggu Berikutnya</h2>
                <ul class="queue-list" id="waitingList">
                    @forelse($initial['waiting'] as $item)
                        <li><span>{{ $item['queue_code'] }}</span><span>{{ $item['status_label'] }}</span></li>
                    @empty
                        <li><span>Tidak ada antrean menunggu</span><span></span></li>
                    @endforelse
                </ul>
                <h2 style="margin-top:1.25rem;">Terakhir Selesai / Dilewati</h2>
                <ul class="queue-list" id="doneList">
                    @forelse($initial['recent_done'] as $item)
                        <li><span>{{ $item['queue_code'] }}</span><span>{{ $item['status_label'] }}</span></li>
                    @empty
                        <li><span>-</span><span></span></li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="stats">
            <div class="stat"><div class="label">Menunggu</div><div class="value" id="waitingCount">{{ $initial['waiting_count'] }}</div></div>
            <div class="stat"><div class="label">Selesai</div><div class="value" id="servedCount">{{ $initial['served_count'] }}</div></div>
            <div class="stat"><div class="label">Dilewati</div><div class="value" id="skippedCount">{{ $initial['skipped_count'] }}</div></div>
            <div class="stat"><div class="label">Total Hari Ini</div><div class="value" id="totalToday">{{ $initial['total_today'] }}</div></div>
        </div>

        <div class="footer">Sistem Antrean Layanan Akademik · Poltekkes Kemenkes Banten</div>
    </div>

    <script>
        const statusUrl = @json(route('queues.status'));
        let soundEnabled = true;
        let lastCalledCode = @json($initial['current']['queue_code'] ?? null);

        const els = {
            date: document.getElementById('displayDate'),
            time: document.getElementById('displayTime'),
            currentCode: document.getElementById('currentCode'),
            currentStatus: document.getElementById('currentStatus'),
            waitingList: document.getElementById('waitingList'),
            doneList: document.getElementById('doneList'),
            waitingCount: document.getElementById('waitingCount'),
            servedCount: document.getElementById('servedCount'),
            skippedCount: document.getElementById('skippedCount'),
            totalToday: document.getElementById('totalToday'),
            soundToggle: document.getElementById('soundToggle'),
        };

        els.soundToggle.addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            els.soundToggle.textContent = 'Suara: ' + (soundEnabled ? 'ON' : 'OFF');
        });

        function renderList(target, items, emptyText) {
            if (!items || !items.length) {
                target.innerHTML = `<li><span>${emptyText}</span><span></span></li>`;
                return;
            }
            target.innerHTML = items.map(item =>
                `<li><span>${item.queue_code}</span><span>${item.status_label}</span></li>`
            ).join('');
        }

        function announce(code) {
            if (!soundEnabled || !code || !('speechSynthesis' in window)) return;
            const utter = new SpeechSynthesisUtterance(
                `Nomor antrean ${code.replace('-', ' ')}, silakan menuju layanan akademik.`
            );
            utter.lang = 'id-ID';
            utter.rate = 0.95;
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utter);
        }

        async function refresh() {
            try {
                const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();

                els.date.textContent = data.date;
                els.time.textContent = data.time;
                els.currentCode.textContent = data.current?.queue_code || '—';
                els.currentStatus.textContent = data.current
                    ? 'Silakan menuju loket layanan akademik'
                    : 'Menunggu pemanggilan antrean';
                els.waitingCount.textContent = data.waiting_count;
                els.servedCount.textContent = data.served_count;
                els.skippedCount.textContent = data.skipped_count;
                els.totalToday.textContent = data.total_today;

                renderList(els.waitingList, data.waiting, 'Tidak ada antrean menunggu');
                renderList(els.doneList, data.recent_done, '-');

                const nextCode = data.current?.queue_code || null;
                if (nextCode && nextCode !== lastCalledCode) {
                    announce(nextCode);
                    lastCalledCode = nextCode;
                } else if (!nextCode) {
                    lastCalledCode = null;
                }
            } catch (e) {
                // keep previous UI on network hiccup
            }
        }

        setInterval(refresh, 3000);
    </script>
</body>
</html>
