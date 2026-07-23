<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\ServiceType;
use App\Models\Submission;
use Illuminate\Support\Collection;

class ProgressStatsService
{
    public function forService(ServiceType $serviceType): array
    {
        $year = now()->year;

        $query = Submission::query()
            ->where('service_type_id', $serviceType->id)
            ->whereYear('created_at', $year);

        $processed = (clone $query)->whereNotIn('status', [
            SubmissionStatus::Submitted->value,
        ])->count();

        $pending = (clone $query)->where('status', SubmissionStatus::Submitted->value)->count();

        return [
            'year' => $year,
            'processed' => $processed,
            'pending' => $pending,
            'total' => $processed + $pending,
        ];
    }

    public function dashboardStats(?int $studyProgramId = null): array
    {
        $year = now()->year;
        $base = Submission::query()->whereYear('created_at', $year);

        if ($studyProgramId !== null) {
            $base->where('study_program_id', $studyProgramId);
        }

        $submissionScope = Submission::query();
        if ($studyProgramId !== null) {
            $submissionScope->where('study_program_id', $studyProgramId);
        }

        $serviceQuery = ServiceType::query()
            ->orderByRaw('is_active DESC')
            ->orderBy('sort_order')
            ->orderBy('name');

        $statusCounts = fn (string $status) => (clone $submissionScope)
            ->where('status', $status)
            ->count();

        return [
            'total' => (clone $submissionScope)->count(),
            'year_total' => (clone $base)->count(),
            'by_status' => [
                'Diajukan' => $statusCounts(SubmissionStatus::Submitted->value),
                'Diproses' => $statusCounts(SubmissionStatus::Processing->value),
                'Diverifikasi ADAK Jurusan' => $statusCounts(SubmissionStatus::JurusanVerified->value),
                'Diverifikasi Admin Pusat / Proses SK' => $statusCounts(SubmissionStatus::CentralVerified->value),
                'Selesai / File Tersedia' => $statusCounts(SubmissionStatus::Completed->value),
                'Terkirim Email' => $statusCounts(SubmissionStatus::EmailSent->value),
                'Ditolak' => $statusCounts(SubmissionStatus::Rejected->value),
            ],
            'by_service' => (clone $serviceQuery)
                ->withCount(['submissions' => fn ($q) => $this->scopeSubmissions($q, $studyProgramId)])
                ->get(),
            'year_by_service' => (clone $serviceQuery)
                ->withCount(['submissions as year_count' => function ($q) use ($year, $studyProgramId) {
                    $q->whereYear('created_at', $year);
                    $this->scopeSubmissions($q, $studyProgramId);
                }])
                ->get(),
            'active_services' => ServiceType::active()
                ->withCount(['submissions' => fn ($q) => $this->scopeSubmissions($q, $studyProgramId)])
                ->withCount(['submissions as year_count' => function ($q) use ($year, $studyProgramId) {
                    $q->whereYear('created_at', $year);
                    $this->scopeSubmissions($q, $studyProgramId);
                }])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ];
    }

    private function scopeSubmissions($query, ?int $studyProgramId): void
    {
        if ($studyProgramId !== null) {
            $query->where('study_program_id', $studyProgramId);
        }
    }

    public function serviceCards(): Collection
    {
        return ServiceType::active()->orderBy('sort_order')->orderBy('id')->get();
    }

    public function progressPageStats(): Collection
    {
        $year = now()->year;

        return ServiceType::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (ServiceType $service) use ($year) {
                $stats = $this->forService($service);

                return [
                    'service' => $service,
                    'year' => $stats['year'],
                    'processed' => $stats['processed'],
                    'pending' => $stats['pending'],
                    'total' => $stats['total'],
                ];
            });
    }
}
