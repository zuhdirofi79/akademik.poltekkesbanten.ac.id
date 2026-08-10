@include('admin.reports.print.layout-start', ['printTitle' => 'Rekap Survei Kepuasan Layanan Akademik'])

<div class="summary">
    <div>Total Responden<strong>{{ $summary['total'] }}</strong></div>
    <div>PUAS<strong>{{ $summary['puas'] }} ({{ $summary['pct_puas'] }}%)</strong></div>
    <div>CUKUP PUAS<strong>{{ $summary['cukup_puas'] }} ({{ $summary['pct_cukup_puas'] }}%)</strong></div>
    <div>KURANG PUAS<strong>{{ $summary['kurang_puas'] }} ({{ $summary['pct_kurang_puas'] }}%)</strong></div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Tiket</th>
            <th>Layanan</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Kepuasan</th>
            <th>Alasan</th>
            <th>Saran</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($row->created_at)?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                <td>{{ $row->ticket_number ?? '-' }}</td>
                <td>{{ $row->serviceDisplayName() }}</td>
                <td>{{ $row->respondent_name ?? '-' }}</td>
                <td>{{ $row->respondent_email ?? '-' }}</td>
                <td>{{ $row->satisfactionLabel() }}</td>
                <td>{{ $row->reason ?? '-' }}</td>
                <td>{{ $row->feedback ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@include('admin.reports.print.layout-end')
