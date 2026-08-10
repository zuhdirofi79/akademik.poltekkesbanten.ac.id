<?php

namespace App\Exports;

use App\Services\AcademicAuditReportService;
use App\Services\ReportFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CombinedReportExport implements WithMultipleSheets
{
    public function __construct(
        private Request $request,
        private AcademicAuditReportService $reports,
        private ReportFilterService $filterService,
    ) {}

    public function sheets(): array
    {
        $payload = $this->reports->combinedReportPayload($this->request);
        $period = $this->filterService->resolvePeriod($this->request);
        $admin = $this->request->user()?->name ?? '-';
        $generatedAt = now(ReportFilterService::TIMEZONE)->format('d/m/Y H:i').' WIB';

        $summaryRows = collect([
            ['Laporan Gabungan Pelayanan Akademik'],
            ['Instansi', 'Poltekkes Kemenkes Banten'],
            ['Periode', $this->filterService->periodLabel($period)],
            ['Waktu Generate', $generatedAt],
            ['Admin', $admin],
            [],
            ['Ringkasan Pengajuan'],
            ['Total Pengajuan', $payload['submission_summary']['total']],
            ['Selesai', $payload['submission_summary']['completed']],
            ['Belum Diproses', $payload['submission_summary']['pending']],
            ['Email Terkirim', $payload['email_sent']],
            ['Email Belum Terkirim', $payload['email_not_sent']],
            [],
            ['Ringkasan Antrean'],
            ['Total Pengunjung', $payload['queue_summary']['total']],
            ['Selesai', $payload['queue_summary']['served']],
            ['Menunggu', $payload['queue_summary']['waiting']],
            ['Rata-rata Tunggu', $payload['queue_summary']['avg_wait_label']],
            [],
            ['Ringkasan Survei'],
            ['Total Responden', $payload['survey_summary']['total']],
            ['PUAS (%)', $payload['survey_summary']['pct_puas']],
            ['CUKUP PUAS (%)', $payload['survey_summary']['pct_cukup_puas']],
            ['KURANG PUAS (%)', $payload['survey_summary']['pct_kurang_puas']],
        ]);

        $unfinished = $payload['unfinished']->values()->map(function ($row, $index) {
            return array_values($this->reports->mapSubmissionRow($row, $index + 1));
        });

        $feedback = $payload['important_feedback']->values()->map(function ($row, $index) {
            return array_values($this->reports->mapSurveyRow($row, $index + 1));
        });

        return [
            new class($summaryRows) implements FromCollection, WithTitle, ShouldAutoSize
            {
                public function __construct(private Collection $rows) {}

                public function collection(): Collection
                {
                    return $this->rows;
                }

                public function title(): string
                {
                    return 'Ringkasan';
                }
            },
            new SubmissionReportExport($this->request, $this->reports),
            new QueueReportExport($this->request, $this->reports),
            new SurveyReportExport($this->request, $this->reports),
            new class($unfinished, $this->reports->submissionHeaders()) implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
            {
                public function __construct(private Collection $rows, private array $headers) {}

                public function collection(): Collection
                {
                    return $this->rows;
                }

                public function headings(): array
                {
                    return $this->headers;
                }

                public function title(): string
                {
                    return 'Pengajuan Belum Selesai';
                }
            },
            new class($feedback, $this->reports->surveyHeaders()) implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
            {
                public function __construct(private Collection $rows, private array $headers) {}

                public function collection(): Collection
                {
                    return $this->rows;
                }

                public function headings(): array
                {
                    return $this->headers;
                }

                public function title(): string
                {
                    return 'Saran Masukan Survei';
                }
            },
        ];
    }
}
