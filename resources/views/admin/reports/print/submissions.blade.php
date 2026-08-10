@include('admin.reports.print.layout-start', ['printTitle' => 'Rekap Pengajuan Layanan Akademik'])

<div class="summary">
    <div>Total<strong>{{ $summary['total'] }}</strong></div>
    <div>Selesai<strong>{{ $summary['completed'] }}</strong></div>
    <div>Belum Diproses<strong>{{ $summary['pending'] }}</strong></div>
    <div>Email Terkirim<strong>{{ $summary['email_sent'] }}</strong></div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Tiket</th>
            <th>Layanan</th>
            <th>Status</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Prodi</th>
            <th>Email</th>
            <th>File Final</th>
            <th>Email Terkirim</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                <td>{{ $row->ticket_number }}</td>
                <td>{{ $row->serviceType?->name }}</td>
                <td>{{ $row->status }}</td>
                <td>{{ $row->full_name }}</td>
                <td>{{ $row->nim }}</td>
                <td>{{ $row->studyProgram?->name }}</td>
                <td>{{ $row->email }}</td>
                <td>{{ $row->finalFiles->isNotEmpty() ? 'Ada' : 'Tidak' }}</td>
                <td>{{ $row->email_sent_at ? 'Ya' : 'Tidak' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@include('admin.reports.print.layout-end')
