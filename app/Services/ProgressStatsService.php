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

    public function dashboardStats(): array
    {
        $year = now()->year;
        $base = Submission::query()->whereYear('created_at', $year);

        $serviceQuery = ServiceType::query()
            ->orderByRaw('is_active DESC')
            ->orderBy('sort_order')
            ->orderBy('name');

        return [
            'total' => Submission::count(),
            'year_total' => (clone $base)->count(),
            'by_status' => [
                'Diajukan' => Submission::where('status', SubmissionStatus::Submitted->value)->count(),
                'Diproses' => Submission::where('status', SubmissionStatus::Processing->value)->count(),
                'Selesai / File Tersedia' => Submission::where('status', SubmissionStatus::Completed->value)->count(),
                'Terkirim Email' => Submission::where('status', SubmissionStatus::EmailSent->value)->count(),
                'Ditolak' => Submission::where('status', SubmissionStatus::Rejected->value)->count(),
            ],
            'by_service' => (clone $serviceQuery)->withCount('submissions')->get(),
            'year_by_service' => (clone $serviceQuery)
                ->withCount(['submissions as year_count' => fn ($q) => $q->whereYear('created_at', $year)])
                ->get(),
            'active_services' => ServiceType::active()
                ->withCount('submissions')
                ->withCount(['submissions as year_count' => fn ($q) => $q->whereYear('created_at', $year)])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ];
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
