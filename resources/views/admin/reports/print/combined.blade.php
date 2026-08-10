@include('admin.reports.print.layout-start', ['printTitle' => 'Laporan Gabungan Pelayanan Akademik'])

<div class="summary">
    <div>Total Pengajuan<strong>{{ $submission_summary['total'] }}</strong></div>
    <div>Pengajuan Selesai<strong>{{ $submission_summary['completed'] }}</strong></div>
    <div>Total Antrean<strong>{{ $queue_summary['total'] }}</strong></div>
    <div>% PUAS<strong>{{ $survey_summary['pct_puas'] }}%</strong></div>
    <div>Email Terkirim<strong>{{ $email_sent }}</strong></div>
    <div>Email Belum Terkirim<strong>{{ $email_not_sent }}</strong></div>
    <div>Responden Survei<strong>{{ $survey_summary['total'] }}</strong></div>
    <div>Belum Selesai<strong>{{ $unfinished->count() }}</strong></div>
</div>

<h3>Layanan Paling Banyak Diajukan</h3>
<table>
    <thead><tr><th>Layanan</th><th>Jumlah</th></tr></thead>
    <tbody>
        @foreach($top_services as $item)
            <tr><td>{{ $item['label'] }}</td><td>{{ $item['total'] }}</td></tr>
        @endforeach
    </tbody>
</table>

<h3>Pengajuan Belum Selesai</h3>
<table>
    <thead>
        <tr>
            <th>Tiket</th>
            <th>Tanggal</th>
            <th>Layanan</th>
            <th>Nama</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($unfinished as $row)
            <tr>
                <td>{{ $row->ticket_number }}</td>
                <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                <td>{{ $row->serviceType?->name }}</td>
                <td>{{ $row->full_name }}</td>
                <td>{{ $row->status }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>

<h3>Saran/Masukan Survei</h3>
<table>
    <thead><tr><th>Tanggal</th><th>Layanan</th><th>Kepuasan</th><th>Saran</th></tr></thead>
    <tbody>
        @forelse($important_feedback as $row)
            <tr>
                <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                <td>{{ $row->serviceDisplayName() }}</td>
                <td>{{ $row->satisfactionLabel() }}</td>
                <td>{{ $row->feedback }}</td>
            </tr>
        @empty
            <tr><td colspan="4">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.reports.print.layout-end')
