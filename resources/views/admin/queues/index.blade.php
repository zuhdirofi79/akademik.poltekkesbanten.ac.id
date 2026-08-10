@extends('layouts.admin')

@section('title', 'Antrean Layanan')

@section('content')
<style>
    .queue-toolbar { display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap; align-items:flex-start; margin-bottom:1rem; }
    .queue-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:0.75rem; margin-bottom:1rem; }
    .queue-grid { display:grid; grid-template-columns:1.1fr 1fr; gap:1rem; }
    .queue-item {
        border:1px solid #e2e8f0; border-radius:10px; padding:0.85rem; margin-bottom:0.65rem; background:#fff;
    }
    .queue-item-head { display:flex; justify-content:space-between; gap:0.5rem; align-items:center; margin-bottom:0.35rem; }
    .queue-code { font-size:1.35rem; font-weight:800; color:#0f766e; }
    .queue-actions { display:flex; flex-wrap:wrap; gap:0.35rem; margin-top:0.55rem; }
    .queue-actions form { display:inline; }
    .queue-note { width:100%; margin-top:0.4rem; }
    .current-box {
        background:linear-gradient(135deg,#ecfdf5,#f0f9ff); border:1px solid #99f6e4; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1rem;
    }
    .current-box .big { font-size:2.5rem; font-weight:800; color:#0f766e; margin:0.25rem 0; }
    @media (max-width: 1000px) { .queue-grid { grid-template-columns:1fr; } }
</style>

<div class="queue-toolbar">
    <div>
        <h1 style="margin-bottom:0.25rem;">Antrean Layanan Akademik</h1>
        <p style="margin:0;color:#64748b;">{{ $todayLabel }} · Update otomatis setiap 3 detik</p>
    </div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <form method="POST" action="{{ route('admin.queues.call-next') }}">
            @csrf
            <button type="submit" class="btn-sm btn-teal">Panggil Antrean Berikutnya</button>
        </form>
        <a href="{{ route('admin.queues.reports') }}" class="btn-sm btn-blue">Laporan</a>
        <a href="{{ route('queues.display') }}" class="btn-sm btn-gray" target="_blank">Buka Display</a>
    </div>
</div>

<div class="current-box">
    <div style="color:#64748b;">Nomor sedang dipanggil / dilayani</div>
    <div class="big" id="currentCode">{{ $initial['current']['queue_code'] ?? '—' }}</div>
    <div id="currentMeta" style="color:#475569;">
        @if(!empty($initial['current']))
            {{ $initial['current']['status_label'] }}
            @if(!empty($initial['current']['visitor_name'])) · {{ $initial['current']['visitor_name'] }} @endif
        @else
            Belum ada nomor yang dipanggil
        @endif
    </div>
</div>

<div class="queue-stats" id="queueStats">
    <div class="stat-box"><h4>Total Hari Ini</h4><div class="value" data-stat="total_today">{{ $initial['total_today'] }}</div></div>
    <div class="stat-box"><h4>Menunggu</h4><div class="value" data-stat="waiting_count">{{ $initial['waiting_count'] }}</div></div>
    <div class="stat-box"><h4>Dipanggil/Dilayani</h4><div class="value" data-stat="active_count">{{ ($initial['called_count'] ?? 0) + ($initial['serving_count'] ?? 0) }}</div></div>
    <div class="stat-box"><h4>Selesai</h4><div class="value" data-stat="served_count">{{ $initial['served_count'] }}</div></div>
    <div class="stat-box"><h4>Dilewati</h4><div class="value" data-stat="skipped_count">{{ $initial['skipped_count'] }}</div></div>
</div>

<div class="queue-grid">
    <div>
        <div class="admin-card">
            <h3>Antrean Menunggu</h3>
            <div id="list-waiting"></div>
        </div>
        <div class="admin-card" style="margin-top:1rem;">
            <h3>Sedang Dipanggil</h3>
            <div id="list-called"></div>
        </div>
        <div class="admin-card" style="margin-top:1rem;">
            <h3>Sedang Dilayani</h3>
            <div id="list-serving"></div>
        </div>
    </div>
    <div>
        <div class="admin-card">
            <h3>Selesai Hari Ini</h3>
            <div id="list-served"></div>
        </div>
        <div class="admin-card" style="margin-top:1rem;">
            <h3>Dilewati Hari Ini</h3>
            <div id="list-skipped"></div>
        </div>
        <div class="admin-card" style="margin-top:1rem;">
            <h3>Dibatalkan</h3>
            <div id="list-cancelled"></div>
        </div>
    </div>
</div>

<script>
(() => {
    const statusUrl = @json(route('admin.queues.status'));
    const csrf = @json(csrf_token());
    const routes = {
        call: @json(url('/admin/queues')) + '/__ID__/call',
        serve: @json(url('/admin/queues')) + '/__ID__/serve',
        finish: @json(url('/admin/queues')) + '/__ID__/finish',
        skip: @json(url('/admin/queues')) + '/__ID__/skip',
        cancel: @json(url('/admin/queues')) + '/__ID__/cancel',
        note: @json(url('/admin/queues')) + '/__ID__/note',
        recall: @json(url('/admin/queues')) + '/__ID__/call',
    };

    const labels = {
        call: 'Panggil',
        recall: 'Panggil Ulang',
        serve: 'Mulai Layani',
        finish: 'Selesai Dilayani',
        skip: 'Lewati',
        cancel: 'Batalkan',
    };

    function actionUrl(action, id) {
        return (routes[action] || '').replace('__ID__', id);
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, s => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[s]));
    }

    function renderItem(item) {
        const actions = (item.actions || []).filter(a => a !== 'note');
        const buttons = actions.map(action => `
            <form method="POST" action="${actionUrl(action, item.id)}" class="js-queue-action">
                <input type="hidden" name="_token" value="${csrf}">
                <button type="submit" class="btn-sm ${action === 'finish' ? 'btn-teal' : (action === 'cancel' ? 'btn-gray' : 'btn-blue')}">${labels[action] || action}</button>
            </form>
        `).join('');

        return `
            <div class="queue-item" data-id="${item.id}">
                <div class="queue-item-head">
                    <div class="queue-code">${escapeHtml(item.queue_code)}</div>
                    <span class="status-badge">${escapeHtml(item.status_label)}</span>
                </div>
                <div style="font-size:0.92rem;color:#475569;">
                    ${escapeHtml(item.visitor_name || 'Tanpa nama')} · ${escapeHtml(item.visitor_type || '-')}
                    <br>${escapeHtml(item.purpose || '-')}
                    <br>Ambil: ${escapeHtml(item.created_at || '-')}
                    ${item.called_at ? ' · Panggil: ' + escapeHtml(item.called_at) : ''}
                </div>
                <div class="queue-actions">${buttons}</div>
                <form method="POST" action="${actionUrl('note', item.id)}" class="js-queue-action">
                    <input type="hidden" name="_token" value="${csrf}">
                    <textarea name="admin_note" class="form-control queue-note" rows="2" placeholder="Catatan admin">${escapeHtml(item.admin_note || '')}</textarea>
                    <button type="submit" class="btn-sm btn-gray" style="margin-top:0.35rem;">Simpan Catatan</button>
                </form>
            </div>
        `;
    }

    function renderList(id, items) {
        const el = document.getElementById(id);
        if (!el) return;
        if (!items || !items.length) {
            el.innerHTML = '<p style="color:#94a3b8;margin:0;">Tidak ada data.</p>';
            return;
        }
        el.innerHTML = items.map(renderItem).join('');
    }

    function applyPayload(data) {
        document.getElementById('currentCode').textContent = data.current?.queue_code || '—';
        const meta = document.getElementById('currentMeta');
        if (data.current) {
            meta.textContent = `${data.current.status_label}${data.current.visitor_name ? ' · ' + data.current.visitor_name : ''}`;
        } else {
            meta.textContent = 'Belum ada nomor yang dipanggil';
        }

        const map = {
            total_today: data.total_today,
            waiting_count: data.waiting_count,
            active_count: (data.called_count || 0) + (data.serving_count || 0),
            served_count: data.served_count,
            skipped_count: data.skipped_count,
        };
        Object.entries(map).forEach(([key, value]) => {
            const el = document.querySelector(`[data-stat="${key}"]`);
            if (el) el.textContent = value;
        });

        renderList('list-waiting', data.lists?.waiting || []);
        renderList('list-called', data.lists?.called || []);
        renderList('list-serving', data.lists?.serving || []);
        renderList('list-served', data.lists?.served || []);
        renderList('list-skipped', data.lists?.skipped || []);
        renderList('list-cancelled', data.lists?.cancelled || []);
    }

    async function refresh() {
        try {
            const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            applyPayload(await res.json());
        } catch (e) {}
    }

    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.js-queue-action');
        if (!form) return;
        e.preventDefault();
        const fd = new FormData(form);
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const data = await res.json();
            if (data.payload) applyPayload(data.payload);
            else refresh();
        } catch (err) {
            form.submit();
        }
    });

    applyPayload(@json($initial));
    setInterval(refresh, 3000);
})();
</script>
@endsection
