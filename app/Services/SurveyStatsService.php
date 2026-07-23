<?php

namespace App\Services;

use App\Models\AcademicSatisfactionSurvey;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SurveyStatsService
{
    public function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('satisfaction_level')) {
            $level = AcademicSatisfactionSurvey::normalizeLevel($request->satisfaction_level);
            $query->where(function (Builder $q) use ($level, $request) {
                $q->where('satisfaction_level', $level)
                    ->orWhere('satisfaction_level', $request->satisfaction_level)
                    ->orWhereRaw('LOWER(REPLACE(satisfaction_level, " ", "_")) = ?', [$level]);
            });
        }

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    public function filteredQuery(Request $request): Builder
    {
        return $this->applyFilters(AcademicSatisfactionSurvey::query(), $request);
    }

    public function summary(Request $request): array
    {
        $base = $this->filteredQuery($request);
        $total = (clone $base)->count();

        $raw = (clone $base)
            ->select('satisfaction_level', DB::raw('COUNT(*) as total'))
            ->groupBy('satisfaction_level')
            ->pluck('total', 'satisfaction_level');

        $puas = 0;
        $cukup = 0;
        $kurang = 0;

        foreach ($raw as $level => $count) {
            $normalized = AcademicSatisfactionSurvey::normalizeLevel((string) $level);
            match ($normalized) {
                'puas' => $puas += (int) $count,
                'cukup_puas' => $cukup += (int) $count,
                'kurang_puas' => $kurang += (int) $count,
                default => null,
            };
        }

        $pct = fn (int $value): float => $total > 0 ? round(($value / $total) * 100, 1) : 0.0;

        return [
            'total' => $total,
            'puas' => $puas,
            'cukup_puas' => $cukup,
            'kurang_puas' => $kurang,
            'pct_puas' => $pct($puas),
            'pct_cukup_puas' => $pct($cukup),
            'pct_kurang_puas' => $pct($kurang),
        ];
    }

    public function byService(Request $request): Collection
    {
        $rows = $this->filteredQuery($request)
            ->leftJoin('service_types', 'service_types.id', '=', 'academic_satisfaction_surveys.service_type_id')
            ->selectRaw("
                academic_satisfaction_surveys.service_type_id,
                COALESCE(service_types.name, academic_satisfaction_surveys.service_name, 'Tidak disebutkan') as service_label,
                COUNT(*) as total,
                SUM(CASE WHEN LOWER(REPLACE(academic_satisfaction_surveys.satisfaction_level, ' ', '_')) IN ('puas') THEN 1 ELSE 0 END) as puas,
                SUM(CASE WHEN LOWER(REPLACE(academic_satisfaction_surveys.satisfaction_level, ' ', '_')) IN ('cukup_puas', 'cukuppuas') THEN 1 ELSE 0 END) as cukup_puas,
                SUM(CASE WHEN LOWER(REPLACE(academic_satisfaction_surveys.satisfaction_level, ' ', '_')) IN ('kurang_puas', 'kurangpuas') THEN 1 ELSE 0 END) as kurang_puas
            ")
            ->groupByRaw("academic_satisfaction_surveys.service_type_id, COALESCE(service_types.name, academic_satisfaction_surveys.service_name, 'Tidak disebutkan')")
            ->orderByDesc('total')
            ->get();

        return $rows->map(function ($row) {
            $total = (int) $row->total;
            $puas = (int) $row->puas;
            $cukup = (int) $row->cukup_puas;
            $kurang = (int) $row->kurang_puas;
            $pct = fn (int $value): float => $total > 0 ? round(($value / $total) * 100, 1) : 0.0;

            return [
                'service_type_id' => $row->service_type_id,
                'service_name' => $row->service_label,
                'total' => $total,
                'puas' => $puas,
                'cukup_puas' => $cukup,
                'kurang_puas' => $kurang,
                'pct_puas' => $pct($puas),
                'pct_cukup_puas' => $pct($cukup),
                'pct_kurang_puas' => $pct($kurang),
            ];
        });
    }

    public function byReason(Request $request): array
    {
        $known = [
            'Layanan Cepat',
            'Petugas Ramah',
            'Layanan Cukup Cepat',
            'Petugas Cukup Ramah',
            'Pelayanan Lama',
            'Petugas Tidak Ramah',
        ];

        $counts = $this->filteredQuery($request)
            ->whereNotNull('reason')
            ->where('reason', '!=', '')
            ->select('reason', DB::raw('COUNT(*) as total'))
            ->groupBy('reason')
            ->pluck('total', 'reason');

        $result = [];
        foreach ($known as $reason) {
            $result[] = [
                'reason' => $reason,
                'total' => (int) ($counts[$reason] ?? 0),
            ];
        }

        return $result;
    }

    public function monthlyTrend(Request $request): array
    {
        $driver = DB::getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = $this->filteredQuery($request)
            ->selectRaw("{$monthExpr} as month_key, COUNT(*) as total")
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get();

        return $rows->map(fn ($row) => [
            'month' => $row->month_key,
            'label' => $this->formatMonthLabel((string) $row->month_key),
            'total' => (int) $row->total,
        ])->all();
    }

    public function recentFeedback(Request $request, int $limit = 50): Collection
    {
        return $this->filteredQuery($request)
            ->with('serviceType')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function periodLabel(Request $request): string
    {
        $from = $request->input('date_from');
        $to = $request->input('date_to');

        if ($from && $to) {
            return $this->formatDateId($from).' s.d. '.$this->formatDateId($to);
        }
        if ($from) {
            return 'Mulai '.$this->formatDateId($from);
        }
        if ($to) {
            return 'Sampai '.$this->formatDateId($to);
        }

        return 'Seluruh Periode';
    }

    public function serviceTypesForFilter(): Collection
    {
        return ServiceType::active()->orderBy('sort_order')->orderBy('id')->get();
    }

    public function chartPayload(Request $request): array
    {
        $summary = $this->summary($request);
        $byService = $this->byService($request);
        $byReason = $this->byReason($request);
        $monthly = $this->monthlyTrend($request);

        return [
            'summary' => $summary,
            'satisfaction' => [
                'labels' => ['PUAS', 'CUKUP PUAS', 'KURANG PUAS'],
                'values' => [$summary['puas'], $summary['cukup_puas'], $summary['kurang_puas']],
                'colors' => ['#16a34a', '#ca8a04', '#dc2626'],
            ],
            'services' => [
                'labels' => $byService->pluck('service_name')->values()->all(),
                'values' => $byService->pluck('total')->values()->all(),
            ],
            'reasons' => [
                'labels' => array_column($byReason, 'reason'),
                'values' => array_column($byReason, 'total'),
            ],
            'monthly' => [
                'labels' => array_column($monthly, 'label'),
                'values' => array_column($monthly, 'total'),
            ],
            'by_service_rows' => $byService,
            'by_reason_rows' => $byReason,
            'monthly_rows' => $monthly,
            'period_label' => $this->periodLabel($request),
        ];
    }

    private function formatMonthLabel(string $monthKey): string
    {
        if (! preg_match('/^(\d{4})-(\d{2})$/', $monthKey, $m)) {
            return $monthKey;
        }

        $months = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ];

        return ($months[$m[2]] ?? $m[2]).' '.$m[1];
    }

    private function formatDateId(string $date): string
    {
        try {
            return \Carbon\Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable) {
            return $date;
        }
    }
}
