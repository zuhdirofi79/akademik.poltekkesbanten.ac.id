@include('admin.reports.print.layout-start', ['printTitle' => 'Rekap Antrean Layanan Akademik'])

<div class="summary">
    <div>Total<strong>{{ $summary['total'] }}</strong></div>
    <div>Selesai<strong>{{ $summary['served'] }}</strong></div>
    <div>Menunggu<strong>{{ $summary['waiting'] }}</strong></div>
    <div>Rata-rata Tunggu<strong>{{ $summary['avg_wait_label'] }}</strong></div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nomor</th>
            <th>Nama</th>
            <th>Jenis</th>
            <th>Keperluan</th>
            <th>Status</th>
            <th>Ambil</th>
            <th>Dipanggil</th>
            <th>Selesai</th>
            <th>Tunggu</th>
            <th>Petugas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($row->queue_date)->format('d/m/Y') }}</td>
                <td>{{ $row->queue_code }}</td>
                <td>{{ $row->visitor_name }}</td>
                <td>{{ $row->visitor_type }}</td>
                <td>{{ $row->purposeDisplay() }}</td>
                <td>{{ $row->statusLabel() }}</td>
                <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                <td>{{ optional($row->called_at)?->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                <td>{{ optional($row->finished_at)?->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                <td>{{ $row->formatDuration($row->waitingSeconds()) }}</td>
                <td>{{ $row->officerName() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@include('admin.reports.print.layout-end')
