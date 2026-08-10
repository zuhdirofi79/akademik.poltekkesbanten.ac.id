<?php

namespace App\Exports;

use App\Services\AcademicAuditReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubmissionReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private Request $request,
        private AcademicAuditReportService $reports,
    ) {}

    public function collection(): Collection
    {
        return $this->reports->submissionQuery($this->request)->get();
    }

    public function headings(): array
    {
        return $this->reports->submissionHeaders();
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return array_values($this->reports->mapSubmissionRow($row, $this->rowNumber));
    }

    public function title(): string
    {
        return 'Pengajuan Layanan';
    }
}
