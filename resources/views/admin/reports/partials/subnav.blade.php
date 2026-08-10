@php $active = $active ?? ''; @endphp
<div class="report-subnav no-print">
    <a href="{{ route('admin.reports.index') }}" class="btn-sm {{ $active === 'index' ? 'btn-teal' : 'btn-gray' }}">Beranda Laporan</a>
    <a href="{{ route('admin.reports.submissions', request()->query()) }}" class="btn-sm {{ $active === 'submissions' ? 'btn-teal' : 'btn-gray' }}">Rekap Pengajuan</a>
    <a href="{{ route('admin.reports.queues', request()->query()) }}" class="btn-sm {{ $active === 'queues' ? 'btn-teal' : 'btn-gray' }}">Rekap Antrean</a>
    <a href="{{ route('admin.reports.surveys', request()->query()) }}" class="btn-sm {{ $active === 'surveys' ? 'btn-teal' : 'btn-gray' }}">Rekap Survei</a>
    <a href="{{ route('admin.reports.combined', request()->query()) }}" class="btn-sm {{ $active === 'combined' ? 'btn-teal' : 'btn-gray' }}">Laporan Gabungan</a>
</div>
