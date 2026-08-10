<?php

namespace App\Services;

use App\Models\AcademicServiceQueue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AcademicQueueReportService
{
    public function __construct(private AcademicQueueService $queueService) {}

    public function applyPeriodDefaults(Request $request): array
    {
        $period = $request->input('period', 'today');
        $today = Carbon::now('Asia/Jakarta')->startOfDay();

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if (! $dateFrom || ! $dateTo) {
            [$dateFrom, $dateTo] = match ($period) {
                'week' => [
                    $today->copy()->startOfWeek(Carbon::MONDAY)->toDateString(),
                    $today->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
                ],
                'month' => [
                    $today->copy()->startOfMonth()->toDateString(),
                    $today->copy()->endOfMonth()->toDateString(),
                ],
                'custom' => [
                    $dateFrom ?: $today->toDateString(),
                    $dateTo ?: $today->toDateString(),
                ],
                default => [$today->toDateString(), $today->toDateString()],
            };
            $period = $period === 'custom' ? 'custom' : ($period ?: 'today');
        } else {
            $period = 'custom';
        }

        return [
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'status' => $request->input('status'),
            'purpose' => $request->input('purpose'),
            'officer_id' => $request->input('officer_id'),
            'visitor_type' => $request->input('visitor_type'),
        ];
    }

    public function filteredQuery(Request $request): Builder
    {
        $filters = $this->applyPeriodDefaults($request);

        $query = AcademicServiceQueue::query()
            ->with(['serviceType', 'calledBy', 'servedBy', 'finishedBy', 'skippedBy', 'cancelledBy'])
            ->whereDate('queue_date', '>=', $filters['date_from'])
            ->whereDate('queue_date', '<=', $filters['date_to']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['visitor_type'])) {
            $query->where('visitor_type', $filters['visitor_type']);
        }

        if (! empty($filters['purpose'])) {
            $purpose = $filters['purpose'];
            $query->where(function ($q) use ($purpose) {
                $q->where('purpose', $purpose)
                    ->orWhere('service_name', $purpose);
            });
        }

        if (! empty($filters['officer_id'])) {
            $officerId = (int) $filters['officer_id'];
            $query->where(function ($q) use ($officerId) {
                $q->where('called_by', $officerId)
                    ->orWhere('served_by', $officerId)
                    ->orWhere('finished_by', $officerId)
                    ->orWhere('skipped_by', $officerId);
            });
        }

        return $query->orderBy('queue_date')->orderBy('queue_number');
    }

    public function reportPayload(Request $request): array
    {
        $filters = $this->applyPeriodDefaults($request);
        $rows = $this->filteredQuery($request)->get();

        $waitSeconds = [];
        $serviceSeconds = [];

        foreach ($rows as $row) {
            $wait = $row->waitingSeconds();
            if ($wait !== null) {
                $waitSeconds[] = $wait;
            }
            $service = $row->serviceSeconds();
            if ($service !== null) {
                $serviceSeconds[] = $service;
            }
        }

        $byDay = $rows->groupBy(fn ($r) => $r->queue_date->format('Y-m-d'))
            ->map(fn (Collection $group, $date) => [
                'date' => $date,
                'label' => Carbon::parse($date)->timezone('Asia/Jakarta')->format('d/m'),
                'total' => $group->count(),
            ])
            ->values();

        $byStatus = collect([
            AcademicServiceQueue::STATUS_SERVED => 'Selesai',
            AcademicServiceQueue::STATUS_WAITING => 'Menunggu',
            AcademicServiceQueue::STATUS_CALLED => 'Dipanggil',
            AcademicServiceQueue::STATUS_SERVING => 'Dilayani',
            AcademicServiceQueue::STATUS_SKIPPED => 'Dilewati',
            AcademicServiceQueue::STATUS_CANCELLED => 'Dibatalkan',
        ])->map(fn ($label, $status) => [
            'status' => $status,
            'label' => $label,
            'total' => $rows->where('status', $status)->count(),
        ])->values();

        $byPurpose = $rows->groupBy(fn ($r) => $r->purposeDisplay())
            ->map(fn (Collection $group, $name) => [
                'label' => $name === '-' ? 'Tidak diisi' : $name,
                'total' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(10);

        $byHour = $rows->groupBy(fn ($r) => optional($r->created_at)?->timezone('Asia/Jakarta')->format('H') ?: '00')
            ->map(fn (Collection $group, $hour) => [
                'hour' => $hour.':00',
                'total' => $group->count(),
            ])
            ->sortKeys()
            ->values();

        $avgWait = count($waitSeconds) ? (int) round(array_sum($waitSeconds) / count($waitSeconds)) : null;
        $avgService = count($serviceSeconds) ? (int) round(array_sum($serviceSeconds) / count($serviceSeconds)) : null;

        $dummy = new AcademicServiceQueue;

        return [
            'filters' => $filters,
            'period_label' => $this->periodLabel($filters),
            'rows' => $rows,
            'summary' => [
                'total' => $rows->count(),
                'served' => $rows->where('status', AcademicServiceQueue::STATUS_SERVED)->count(),
                'waiting' => $rows->where('status', AcademicServiceQueue::STATUS_WAITING)->count(),
                'skipped' => $rows->where('status', AcademicServiceQueue::STATUS_SKIPPED)->count(),
                'cancelled' => $rows->where('status', AcademicServiceQueue::STATUS_CANCELLED)->count(),
                'avg_wait_seconds' => $avgWait,
                'avg_service_seconds' => $avgService,
                'avg_wait_label' => $dummy->formatDuration($avgWait),
                'avg_service_label' => $dummy->formatDuration($avgService),
            ],
            'charts' => [
                'by_day' => $byDay,
                'by_status' => $byStatus,
                'by_purpose' => $byPurpose,
                'by_hour' => $byHour,
            ],
            'officers' => User::query()
                ->whereIn('role', ['admin', 'superadmin', 'admin_jurusan'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'purposes' => $this->queueService->purposeOptions()->pluck('name')->unique()->values(),
        ];
    }

    private function periodLabel(array $filters): string
    {
        $from = Carbon::parse($filters['date_from'])->format('d/m/Y');
        $to = Carbon::parse($filters['date_to'])->format('d/m/Y');

        return match ($filters['period']) {
            'today' => 'Hari ini ('.$from.')',
            'week' => 'Minggu ini ('.$from.' - '.$to.')',
            'month' => 'Bulan ini ('.$from.' - '.$to.')',
            default => $from.' - '.$to,
        };
    }
}
