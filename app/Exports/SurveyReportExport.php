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

class SurveyReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private Request $request,
        private AcademicAuditReportService $reports,
    ) {}

    public function collection(): Collection
    {
        return $this->reports->surveyQuery($this->request)->get();
    }

    public function headings(): array
    {
        return $this->reports->surveyHeaders();
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return array_values($this->reports->mapSurveyRow($row, $this->rowNumber));
    }

    public function title(): string
    {
        return 'Survei Kepuasan';
    }
}
