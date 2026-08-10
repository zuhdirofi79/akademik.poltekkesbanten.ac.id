<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AcademicQueueReportService;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QueueReportController extends Controller
{
    public function __construct(
        private AcademicQueueReportService $reports,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request)
    {
        $payload = $this->reports->reportPayload($request);

        return view('admin.queues.reports', $payload);
    }

    public function print(Request $request)
    {
        $payload = $this->reports->reportPayload($request);
        $payload['generatedAt'] = now('Asia/Jakarta');

        return view('admin.queues.print', $payload);
    }

    public function export(Request $request): StreamedResponse
    {
        $payload = $this->reports->reportPayload($request);
        $filename = 'laporan-antrean-layanan-akademik-'.now('Asia/Jakarta')->format('Ymd').'.csv';

        $this->activityLogger->log(
            'queue_export',
            'Export laporan antrean layanan akademik periode '.$payload['period_label'].'.'
        );

        return response()->streamDownload(function () use ($payload) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Tanggal',
                'Nomor Antrean',
                'Nama Pengunjung',
                'Identitas',
                'Jenis Pengunjung',
                'Keperluan Layanan',
                'Status',
                'Jam Ambil',
                'Jam Dipanggil',
                'Jam Mulai Dilayani',
                'Jam Selesai',
                'Waktu Tunggu',
                'Waktu Layanan',
                'Petugas',
                'Catatan',
            ]);

            $no = 1;
            foreach ($payload['rows'] as $row) {
                fputcsv($handle, [
                    $no++,
                    $row->queue_date->format('Y-m-d'),
                    $row->queue_code,
                    $row->visitor_name ?? '-',
                    $row->visitor_identity ?? '-',
                    $row->visitor_type ?? '-',
                    $row->purposeDisplay(),
                    $row->statusLabel(),
                    optional($row->created_at)?->timezone('Asia/Jakarta')->format('H:i:s') ?? '-',
                    optional($row->called_at)?->timezone('Asia/Jakarta')->format('H:i:s') ?? '-',
                    optional($row->served_at)?->timezone('Asia/Jakarta')->format('H:i:s') ?? '-',
                    optional($row->finished_at)?->timezone('Asia/Jakarta')->format('H:i:s') ?? '-',
                    $row->formatDuration($row->waitingSeconds()),
                    $row->formatDuration($row->serviceSeconds()),
                    $row->officerName(),
                    $row->admin_note ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
