<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CombinedReportExport;
use App\Exports\QueueReportExport;
use App\Exports\SubmissionReportExport;
use App\Exports\SurveyReportExport;
use App\Http\Controllers\Controller;
use App\Models\ReportExportLog;
use App\Services\AcademicAuditReportService;
use App\Services\AdminActivityLogger;
use App\Services\ReportFilterService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private AcademicAuditReportService $reports,
        private ReportFilterService $filters,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request)
    {
        $summary = $this->reports->dashboardSummary($request);

        return view('admin.reports.index', [
            'summary' => $summary,
            'options' => $this->reports->filterOptions($request->user()),
            'filters' => array_merge($summary['period'], $request->except(['page'])),
            'exportLogs' => ReportExportLog::query()
                ->with('user')
                ->latest('exported_at')
                ->limit(10)
                ->get(),
        ]);
    }

    public function submissions(Request $request)
    {
        return view('admin.reports.submissions', $this->reports->submissionReportPayload($request));
    }

    public function queues(Request $request)
    {
        return view('admin.reports.queues', $this->reports->queueReportPayload($request));
    }

    public function surveys(Request $request)
    {
        return view('admin.reports.surveys', $this->reports->surveyReportPayload($request));
    }

    public function combined(Request $request)
    {
        return view('admin.reports.combined', $this->reports->combinedReportPayload($request));
    }

    public function exportSubmissions(Request $request): BinaryFileResponse|StreamedResponse
    {
        return $this->export(
            $request,
            'submissions',
            'laporan-pengajuan-layanan-akademik',
            new SubmissionReportExport($request, $this->reports),
            fn () => $this->streamCsv($request, 'submissions')
        );
    }

    public function exportQueues(Request $request): BinaryFileResponse|StreamedResponse
    {
        return $this->export(
            $request,
            'queues',
            'laporan-antrean-layanan-akademik',
            new QueueReportExport($request, $this->reports),
            fn () => $this->streamCsv($request, 'queues')
        );
    }

    public function exportSurveys(Request $request): BinaryFileResponse|StreamedResponse
    {
        return $this->export(
            $request,
            'surveys',
            'laporan-survei-kepuasan-layanan-akademik',
            new SurveyReportExport($request, $this->reports),
            fn () => $this->streamCsv($request, 'surveys')
        );
    }

    public function exportCombined(Request $request): BinaryFileResponse|StreamedResponse
    {
        return $this->export(
            $request,
            'combined',
            'laporan-gabungan-pelayanan-akademik-poltekkes-banten',
            new CombinedReportExport($request, $this->reports, $this->filters),
            fn () => $this->streamCsv($request, 'combined')
        );
    }

    public function printSubmissions(Request $request)
    {
        $payload = $this->reports->submissionReportPayload($request, false);
        $payload['generatedAt'] = now(ReportFilterService::TIMEZONE);
        $payload['printedBy'] = $request->user()?->name;

        return view('admin.reports.print.submissions', $payload);
    }

    public function printQueues(Request $request)
    {
        $payload = $this->reports->queueReportPayload($request, false);
        $payload['generatedAt'] = now(ReportFilterService::TIMEZONE);
        $payload['printedBy'] = $request->user()?->name;

        return view('admin.reports.print.queues', $payload);
    }

    public function printSurveys(Request $request)
    {
        $payload = $this->reports->surveyReportPayload($request, false);
        $payload['generatedAt'] = now(ReportFilterService::TIMEZONE);
        $payload['printedBy'] = $request->user()?->name;

        return view('admin.reports.print.surveys', $payload);
    }

    public function printCombined(Request $request)
    {
        $payload = $this->reports->combinedReportPayload($request);
        $payload['generatedAt'] = now(ReportFilterService::TIMEZONE);
        $payload['printedBy'] = $request->user()?->name;

        return view('admin.reports.print.combined', $payload);
    }

    private function export(
        Request $request,
        string $type,
        string $basename,
        object $excelExport,
        callable $csvFallback
    ): BinaryFileResponse|StreamedResponse {
        $format = strtolower((string) $request->input('format', 'xlsx'));
        $stamp = now(ReportFilterService::TIMEZONE)->format('Ymd-Hi');

        if ($format === 'csv') {
            $filename = "{$basename}-{$stamp}.csv";
            $this->logExport($request, $type, 'csv', $filename);

            return $csvFallback();
        }

        $filename = "{$basename}-{$stamp}.xlsx";
        $this->logExport($request, $type, 'xlsx', $filename);

        return Excel::download($excelExport, $filename);
    }

    private function streamCsv(Request $request, string $type): StreamedResponse
    {
        $period = $this->filters->resolvePeriod($request);
        $periodLabel = $this->filters->periodLabel($period);
        $stamp = now(ReportFilterService::TIMEZONE)->format('Ymd-Hi');
        $admin = $request->user()?->name ?? '-';
        $generatedAt = now(ReportFilterService::TIMEZONE)->format('d/m/Y H:i').' WIB';

        [$basename, $headers, $mapper, $query] = match ($type) {
            'queues' => [
                'laporan-antrean-layanan-akademik',
                $this->reports->queueHeaders(),
                fn ($row, $no) => array_values($this->reports->mapQueueRow($row, $no)),
                $this->reports->queueQuery($request),
            ],
            'surveys' => [
                'laporan-survei-kepuasan-layanan-akademik',
                $this->reports->surveyHeaders(),
                fn ($row, $no) => array_values($this->reports->mapSurveyRow($row, $no)),
                $this->reports->surveyQuery($request),
            ],
            'combined' => [
                'laporan-gabungan-pelayanan-akademik-poltekkes-banten',
                $this->reports->submissionHeaders(),
                fn ($row, $no) => array_values($this->reports->mapSubmissionRow($row, $no)),
                $this->reports->submissionQuery($request),
            ],
            default => [
                'laporan-pengajuan-layanan-akademik',
                $this->reports->submissionHeaders(),
                fn ($row, $no) => array_values($this->reports->mapSubmissionRow($row, $no)),
                $this->reports->submissionQuery($request),
            ],
        };

        $title = match ($type) {
            'queues' => 'Laporan Antrean Layanan Akademik',
            'surveys' => 'Laporan Survei Kepuasan Layanan Akademik',
            'combined' => 'Laporan Gabungan Pelayanan Akademik',
            default => 'Laporan Pengajuan Layanan Akademik',
        };

        $filename = "{$basename}-{$stamp}.csv";

        return response()->streamDownload(function () use ($headers, $mapper, $query, $title, $periodLabel, $generatedAt, $admin, $type, $request) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [$title]);
            fputcsv($handle, ['Instansi', 'Poltekkes Kemenkes Banten']);
            fputcsv($handle, ['Periode', $periodLabel]);
            fputcsv($handle, ['Waktu Generate', $generatedAt]);
            fputcsv($handle, ['Admin', $admin]);
            fputcsv($handle, []);

            if ($type === 'combined') {
                $payload = $this->reports->combinedReportPayload($request);
                fputcsv($handle, ['=== RINGKASAN ===']);
                fputcsv($handle, ['Total Pengajuan', $payload['submission_summary']['total']]);
                fputcsv($handle, ['Pengajuan Selesai', $payload['submission_summary']['completed']]);
                fputcsv($handle, ['Total Antrean', $payload['queue_summary']['total']]);
                fputcsv($handle, ['Responden Survei', $payload['survey_summary']['total']]);
                fputcsv($handle, ['Persentase PUAS', $payload['survey_summary']['pct_puas'].'%']);
                fputcsv($handle, []);
                fputcsv($handle, ['=== PENGAJUAN LAYANAN ===']);
            }

            fputcsv($handle, $headers);

            $no = 0;
            $query->chunk(200, function ($rows) use ($handle, $mapper, &$no) {
                foreach ($rows as $row) {
                    $no++;
                    fputcsv($handle, $mapper($row, $no));
                }
            });

            if ($type === 'combined') {
                fputcsv($handle, []);
                fputcsv($handle, ['=== ANTREAN LAYANAN ===']);
                fputcsv($handle, $this->reports->queueHeaders());
                $qNo = 0;
                $this->reports->queueQuery($request)->chunk(200, function ($rows) use ($handle, &$qNo) {
                    foreach ($rows as $row) {
                        $qNo++;
                        fputcsv($handle, array_values($this->reports->mapQueueRow($row, $qNo)));
                    }
                });

                fputcsv($handle, []);
                fputcsv($handle, ['=== SURVEI KEPUASAN ===']);
                fputcsv($handle, $this->reports->surveyHeaders());
                $sNo = 0;
                $this->reports->surveyQuery($request)->chunk(200, function ($rows) use ($handle, &$sNo) {
                    foreach ($rows as $row) {
                        $sNo++;
                        fputcsv($handle, array_values($this->reports->mapSurveyRow($row, $sNo)));
                    }
                });
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function logExport(Request $request, string $type, string $format, string $filename): void
    {
        ReportExportLog::create([
            'user_id' => $request->user()?->id,
            'report_type' => $type,
            'format' => $format,
            'filters' => $request->except(['_token']),
            'file_name' => $filename,
            'exported_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $this->activityLogger->log(
            'report_export',
            "Export laporan {$type} ({$format}): {$filename}"
        );
    }
}
