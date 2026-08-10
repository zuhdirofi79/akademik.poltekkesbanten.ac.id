<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\AcademicSatisfactionSurvey;
use App\Models\AcademicServiceQueue;
use App\Models\ServiceType;
use App\Models\StudyProgram;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AcademicAuditReportService
{
    public function __construct(
        private ReportFilterService $filters,
        private AcademicQueueReportService $queueReports,
        private SurveyStatsService $surveyStats,
    ) {}

    public function filterOptions(?User $user = null): array
    {
        $studyPrograms = StudyProgram::query()->orderBy('name')->get(['id', 'name']);
        if ($user?->isAdminJurusan() && $user->study_program_id) {
            $studyPrograms = $studyPrograms->where('id', $user->study_program_id)->values();
        }

        return [
            'period_options' => $this->filters->periodOptions(),
            'service_types' => ServiceType::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'study_programs' => $studyPrograms,
            'statuses' => SubmissionStatus::adminOptions(),
            'officers' => User::query()
                ->whereIn('role', ['admin', 'superadmin', 'admin_jurusan'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'satisfaction_levels' => [
                'puas' => 'PUAS',
                'cukup_puas' => 'CUKUP PUAS',
                'kurang_puas' => 'KURANG PUAS',
            ],
            'queue_statuses' => [
                'waiting' => 'Menunggu',
                'called' => 'Dipanggil',
                'serving' => 'Sedang Dilayani',
                'served' => 'Selesai',
                'skipped' => 'Dilewati',
                'cancelled' => 'Dibatalkan',
            ],
            'visitor_types' => AcademicServiceQueue::VISITOR_TYPES,
        ];
    }

    public function dashboardSummary(Request $request): array
    {
        $period = $this->filters->resolvePeriod($request);
        $submissionQuery = $this->baseSubmissionQuery($request);
        $this->filters->applyDateFilter($submissionQuery, $request, 'created_at');

        $completedStatuses = [
            SubmissionStatus::Completed->value,
            SubmissionStatus::EmailSent->value,
        ];

        $total = (clone $submissionQuery)->count();
        $completed = (clone $submissionQuery)->whereIn('status', $completedStatuses)->count();
        $pending = (clone $submissionQuery)->where('status', SubmissionStatus::Submitted->value)->count();
        $emailSent = (clone $submissionQuery)->whereNotNull('email_sent_at')->count();

        $today = Carbon::now(ReportFilterService::TIMEZONE)->toDateString();
        $queueToday = AcademicServiceQueue::query()->whereDate('queue_date', $today)->count();
        $queueServed = AcademicServiceQueue::query()
            ->whereDate('queue_date', '>=', $period['start_date'])
            ->whereDate('queue_date', '<=', $period['end_date'])
            ->where('status', AcademicServiceQueue::STATUS_SERVED)
            ->count();

        $surveyQuery = AcademicSatisfactionSurvey::query();
        $this->filters->applyDateFilter($surveyQuery, $request, 'created_at');
        $surveySummary = $this->surveyStats->summary($this->requestWithPeriod($request, $period));

        return [
            'period' => $period,
            'period_label' => $this->filters->periodLabel($period),
            'cards' => [
                'total_submissions' => $total,
                'completed_submissions' => $completed,
                'pending_submissions' => $pending,
                'email_sent' => $emailSent,
                'queue_today' => $queueToday,
                'queue_served' => $queueServed,
                'survey_respondents' => $surveySummary['total'],
                'survey_pct_puas' => $surveySummary['pct_puas'],
            ],
        ];
    }

    public function applySubmissionFilters(Builder $query, Request $request): Builder
    {
        $user = $request->user();
        if ($user?->isAdminJurusan() && $user->study_program_id) {
            $query->where('submissions.study_program_id', $user->study_program_id);
        }

        $this->filters->applyDateFilter($query, $request, 'submissions.created_at');

        foreach ([
            'ticket_number' => 'ticket_number',
            'full_name' => 'full_name',
            'nim' => 'nim',
            'email' => 'email',
            'whatsapp' => 'whatsapp',
        ] as $input => $column) {
            if ($request->filled($input)) {
                $query->where("submissions.{$column}", 'like', '%'.$request->input($input).'%');
            }
        }

        if ($request->filled('identity')) {
            $identity = $request->input('identity');
            $query->where(function (Builder $q) use ($identity) {
                $q->where('submissions.nim', 'like', "%{$identity}%")
                    ->orWhere('submissions.nik', 'like', "%{$identity}%");
            });
        }

        if ($request->filled('study_program_id')) {
            $query->where('submissions.study_program_id', $request->study_program_id);
        }

        if ($request->filled('service_type_id')) {
            $query->where('submissions.service_type_id', $request->service_type_id);
        }

        if ($request->filled('status')) {
            $query->where('submissions.status', $request->status);
        }

        if ($request->filled('email_sent')) {
            match ($request->input('email_sent')) {
                'yes' => $query->whereNotNull('submissions.email_sent_at'),
                'no' => $query->whereNull('submissions.email_sent_at'),
                default => null,
            };
        }

        if ($request->filled('has_final_file')) {
            match ($request->input('has_final_file')) {
                'yes' => $query->whereHas('finalFiles'),
                'no' => $query->whereDoesntHave('finalFiles'),
                default => null,
            };
        }

        if ($request->filled('officer_id')) {
            $officerId = (int) $request->officer_id;
            $query->whereHas('statusHistories', fn (Builder $q) => $q->where('changed_by', $officerId));
        }

        if ($request->filled('jurusan_verified')) {
            match ($request->input('jurusan_verified')) {
                'yes' => $query->whereNotNull('submissions.jurusan_verified_at'),
                'no' => $query->whereNull('submissions.jurusan_verified_at')
                    ->whereHas('serviceType', fn ($q) => $q->whereIn('slug', ['surat-cuti-mahasiswa', 'surat-keluar-mahasiswa'])),
                default => null,
            };
        }

        if ($request->filled('central_verified')) {
            match ($request->input('central_verified')) {
                'yes' => $query->whereNotNull('submissions.central_verified_at'),
                'no' => $query->whereNull('submissions.central_verified_at')
                    ->whereHas('serviceType', fn ($q) => $q->whereIn('slug', ['surat-cuti-mahasiswa', 'surat-keluar-mahasiswa'])),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('submissions.nim', 'like', "%{$search}%")
                    ->orWhere('submissions.full_name', 'like', "%{$search}%")
                    ->orWhere('submissions.email', 'like', "%{$search}%")
                    ->orWhere('submissions.whatsapp', 'like', "%{$search}%")
                    ->orWhere('submissions.ticket_number', 'like', "%{$search}%")
                    ->orWhere('submissions.nik', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function submissionQuery(Request $request): Builder
    {
        return $this->applySubmissionFilters(
            Submission::query()->from('submissions'),
            $request
        )->with([
            'serviceType',
            'studyProgram',
            'finalFiles',
            'satisfactionSurvey',
            'jurusanVerifiedBy',
            'centralVerifiedBy',
            'statusHistories' => fn ($q) => $q->with('changedByUser')->latest(),
        ])->latest('submissions.created_at');
    }

    public function submissionReportPayload(Request $request, bool $paginate = true): array
    {
        $period = $this->filters->resolvePeriod($request);
        $query = $this->submissionQuery($request);
        $rows = $paginate
            ? $query->paginate(25)->withQueryString()
            : $query->get();

        $statsQuery = $this->submissionQuery($request);
        $completedStatuses = [
            SubmissionStatus::Completed->value,
            SubmissionStatus::EmailSent->value,
        ];

        $chartRows = (clone $statsQuery)->with('serviceType')->get(['id', 'service_type_id', 'status', 'created_at']);

        $byService = $chartRows
            ->groupBy(fn ($row) => $row->serviceType?->name ?? 'Tidak diketahui')
            ->map(fn (Collection $group, $label) => [
                'label' => $label,
                'total' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $byStatus = $chartRows
            ->groupBy('status')
            ->map(fn (Collection $group, $label) => [
                'label' => $label,
                'total' => $group->count(),
            ])
            ->values();

        $byMonth = $chartRows
            ->groupBy(fn ($row) => $row->created_at?->timezone(ReportFilterService::TIMEZONE)->format('Y-m') ?: '-')
            ->map(fn (Collection $group, $key) => [
                'label' => $key,
                'total' => $group->count(),
            ])
            ->sortKeys()
            ->values();

        return [
            'filters' => array_merge($period, $request->except(['page'])),
            'period_label' => $this->filters->periodLabel($period),
            'rows' => $rows,
            'summary' => [
                'total' => (clone $statsQuery)->count(),
                'completed' => (clone $statsQuery)->whereIn('status', $completedStatuses)->count(),
                'pending' => (clone $statsQuery)->where('status', SubmissionStatus::Submitted->value)->count(),
                'email_sent' => (clone $statsQuery)->whereNotNull('email_sent_at')->count(),
                'with_final' => (clone $statsQuery)->whereHas('finalFiles')->count(),
            ],
            'charts' => [
                'by_service' => $byService,
                'by_status' => $byStatus,
                'by_month' => $byMonth,
            ],
            'options' => $this->filterOptions($request->user()),
        ];
    }

    public function mapSubmissionRow(Submission $row, int $no): array
    {
        $tz = ReportFilterService::TIMEZONE;
        $processed = $row->statusHistories
            ->first(fn ($h) => in_array($h->new_status, [
                SubmissionStatus::Processing->value,
                SubmissionStatus::JurusanVerified->value,
                SubmissionStatus::CentralVerified->value,
                SubmissionStatus::Approved->value,
                SubmissionStatus::Completed->value,
                SubmissionStatus::EmailSent->value,
            ], true));

        $completedHistory = $row->statusHistories
            ->first(fn ($h) => in_array($h->new_status, [
                SubmissionStatus::Completed->value,
                SubmissionStatus::EmailSent->value,
            ], true));

        $finalFile = $row->finalFiles->sortByDesc('created_at')->first();
        $survey = $row->satisfactionSurvey;
        $progressUrl = $row->progress_token
            ? url('/progres/'.$row->progress_token)
            : '-';

        return [
            'No' => $no,
            'Tanggal Pengajuan' => $row->created_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Nomor Tiket' => $row->ticket_number,
            'Jenis Layanan' => $row->serviceType?->name ?? '-',
            'Status Pengajuan' => $row->status,
            'Nama Pengaju' => $row->full_name,
            'NIM' => $row->nim ?? '-',
            'NIK' => $row->nik ?? '-',
            'Program Studi' => $row->studyProgram?->name ?? '-',
            'Semester' => $row->semester ?? '-',
            'Email Pengaju' => $row->email ?? '-',
            'Nomor HP/Telepon' => $row->whatsapp ?? '-',
            'Keterangan/Keperluan Surat' => $row->purpose ?? $row->additional_note ?? '-',
            'Tanggal Diproses' => $processed?->created_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Tanggal Selesai' => $completedHistory?->created_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Admin Pemroses' => $processed?->changedByUser?->name ?? '-',
            'Catatan Admin' => $row->admin_note ?? '-',
            'File Final Ada/Tidak' => $row->finalFiles->isNotEmpty() ? 'Ada' : 'Tidak',
            'Tanggal Upload File Final' => $finalFile?->created_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Email Terkirim' => $row->email_sent_at ? 'Ya' : 'Tidak',
            'Tanggal Email Terkirim' => $row->email_sent_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Error Email' => $row->email_error ?? '-',
            'Link Progres' => $progressUrl,
            'Survei Kepuasan' => $survey ? 'Sudah' : 'Belum',
            'Tingkat Kepuasan' => $survey?->satisfactionLabel() ?? '-',
            'Alasan Kepuasan' => $survey?->reason ?? '-',
            'Saran/Masukan' => $survey?->feedback ?? '-',
            'Verifikasi ADAK Jurusan' => $row->jurusan_verified_at ? 'Sudah' : 'Belum',
            'Tanggal Verifikasi Jurusan' => $row->jurusan_verified_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Verifikator Jurusan' => $row->jurusanVerifiedBy?->name ?? '-',
            'Catatan Verifikasi Jurusan' => $row->jurusan_verification_note ?? '-',
            'Verifikasi Admin Pusat' => $row->central_verified_at ? 'Sudah' : 'Belum',
            'Tanggal Verifikasi Pusat' => $row->central_verified_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Verifikator Pusat' => $row->centralVerifiedBy?->name ?? '-',
            'Catatan Verifikasi Pusat' => $row->central_verification_note ?? '-',
            'Nama Kegiatan' => $row->activity_name ?? '-',
            'Tanggal Kegiatan' => $row->activity_date?->format('d/m/Y') ?? '-',
            'Lokasi Kegiatan' => $row->activity_location ?? '-',
            'Daftar Peserta' => $row->participant_list ?? '-',
            'Status Sebelumnya' => $row->previous_status ?? '-',
            'Alasan Pengaktifan Kembali' => $row->reactivation_reason ?? '-',
        ];
    }

    public function applyQueueFilters(Builder $query, Request $request): Builder
    {
        $period = $this->filters->resolvePeriod($request);
        $query->whereDate('queue_date', '>=', $period['start_date'])
            ->whereDate('queue_date', '<=', $period['end_date']);

        if ($request->filled('queue_number')) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('queue_code', 'like', '%'.$request->queue_number.'%')
                    ->orWhere('queue_number', 'like', '%'.$request->queue_number.'%');
            });
        }

        if ($request->filled('visitor_name')) {
            $query->where('visitor_name', 'like', '%'.$request->visitor_name.'%');
        }

        if ($request->filled('visitor_identity')) {
            $query->where('visitor_identity', 'like', '%'.$request->visitor_identity.'%');
        }

        if ($request->filled('visitor_type')) {
            $query->where('visitor_type', $request->visitor_type);
        }

        if ($request->filled('purpose')) {
            $purpose = $request->purpose;
            $query->where(function (Builder $q) use ($purpose) {
                $q->where('purpose', $purpose)
                    ->orWhere('service_name', $purpose);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('officer_id')) {
            $officerId = (int) $request->officer_id;
            $query->where(function (Builder $q) use ($officerId) {
                $q->where('called_by', $officerId)
                    ->orWhere('served_by', $officerId)
                    ->orWhere('finished_by', $officerId)
                    ->orWhere('skipped_by', $officerId);
            });
        }

        if ($request->filled('hour')) {
            $hour = str_pad((string) ((int) $request->hour), 2, '0', STR_PAD_LEFT);
            if (DB::getDriverName() === 'sqlite') {
                $query->whereRaw("strftime('%H', datetime(created_at, '+7 hours')) = ?", [$hour]);
            } else {
                $query->whereRaw("DATE_FORMAT(CONVERT_TZ(created_at, '+00:00', '+07:00'), '%H') = ?", [$hour]);
            }
        }

        return $query;
    }

    public function queueQuery(Request $request): Builder
    {
        return $this->applyQueueFilters(AcademicServiceQueue::query(), $request)
            ->with(['serviceType', 'calledBy', 'servedBy', 'finishedBy', 'skippedBy', 'cancelledBy'])
            ->orderBy('queue_date')
            ->orderBy('queue_number');
    }

    public function queueReportPayload(Request $request, bool $paginate = true): array
    {
        $adapted = $this->adaptQueueRequest($request);
        $base = $this->queueReports->reportPayload($adapted);
        $query = $this->queueQuery($request);

        $rows = $paginate
            ? $query->paginate(25)->withQueryString()
            : $query->get();

        $allRows = $paginate ? $this->queueQuery($request)->get() : $rows;
        $waitSeconds = [];
        $serviceSeconds = [];
        foreach ($allRows as $row) {
            $wait = $row->waitingSeconds();
            if ($wait !== null) {
                $waitSeconds[] = $wait;
            }
            $service = $row->serviceSeconds();
            if ($service !== null) {
                $serviceSeconds[] = $service;
            }
        }

        $dummy = new AcademicServiceQueue;
        $avgWait = count($waitSeconds) ? (int) round(array_sum($waitSeconds) / count($waitSeconds)) : null;
        $avgService = count($serviceSeconds) ? (int) round(array_sum($serviceSeconds) / count($serviceSeconds)) : null;
        $byHourGrouped = $allRows
            ->groupBy(fn ($r) => $r->created_at?->timezone(ReportFilterService::TIMEZONE)->format('H') ?: '00');
        $peakHour = $byHourGrouped->map->count()->sortDesc()->keys()->first();

        $topPurpose = $allRows
            ->groupBy(fn ($r) => $r->purposeDisplay())
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();

        $period = $this->filters->resolvePeriod($request);

        $charts = [
            'by_day' => $allRows->groupBy(fn ($r) => $r->queue_date?->format('Y-m-d') ?: '-')
                ->map(fn (Collection $group, $date) => [
                    'date' => $date,
                    'label' => $date === '-' ? '-' : Carbon::parse($date)->format('d/m'),
                    'total' => $group->count(),
                ])->values(),
            'by_status' => collect([
                AcademicServiceQueue::STATUS_SERVED => 'Selesai',
                AcademicServiceQueue::STATUS_WAITING => 'Menunggu',
                AcademicServiceQueue::STATUS_CALLED => 'Dipanggil',
                AcademicServiceQueue::STATUS_SERVING => 'Dilayani',
                AcademicServiceQueue::STATUS_SKIPPED => 'Dilewati',
                AcademicServiceQueue::STATUS_CANCELLED => 'Dibatalkan',
            ])->map(fn ($label, $status) => [
                'status' => $status,
                'label' => $label,
                'total' => $allRows->where('status', $status)->count(),
            ])->values(),
            'by_hour' => $byHourGrouped->map(fn (Collection $group, $hour) => [
                'hour' => $hour.':00',
                'total' => $group->count(),
            ])->sortKeys()->values(),
        ];

        return [
            'filters' => array_merge($period, $request->except(['page'])),
            'period_label' => $this->filters->periodLabel($period),
            'rows' => $rows,
            'summary' => [
                'total' => $allRows->count(),
                'waiting' => $allRows->where('status', AcademicServiceQueue::STATUS_WAITING)->count(),
                'called' => $allRows->where('status', AcademicServiceQueue::STATUS_CALLED)->count(),
                'serving' => $allRows->where('status', AcademicServiceQueue::STATUS_SERVING)->count(),
                'served' => $allRows->where('status', AcademicServiceQueue::STATUS_SERVED)->count(),
                'skipped' => $allRows->where('status', AcademicServiceQueue::STATUS_SKIPPED)->count(),
                'cancelled' => $allRows->where('status', AcademicServiceQueue::STATUS_CANCELLED)->count(),
                'avg_wait_label' => $dummy->formatDuration($avgWait),
                'avg_service_label' => $dummy->formatDuration($avgService),
                'peak_hour' => $peakHour ? $peakHour.':00' : '-',
                'top_purpose' => $topPurpose ?: '-',
            ],
            'charts' => $charts,
            'options' => $this->filterOptions($request->user()),
            'purposes' => $base['purposes'],
        ];
    }

    public function mapQueueRow(AcademicServiceQueue $row, int $no): array
    {
        $tz = ReportFilterService::TIMEZONE;

        return [
            'No' => $no,
            'Tanggal' => $row->queue_date?->format('d/m/Y') ?? '-',
            'Nomor Antrean' => $row->queue_code,
            'Nama Pengunjung' => $row->visitor_name ?? '-',
            'Identitas' => $row->visitor_identity ?? '-',
            'Nomor HP/Telepon' => $row->visitor_phone ?? '-',
            'Jenis Pengunjung' => $row->visitor_type ?? '-',
            'Keperluan Layanan' => $row->purposeDisplay(),
            'Status' => $row->statusLabel(),
            'Jam Ambil Nomor' => $row->created_at?->timezone($tz)->format('H:i:s') ?? '-',
            'Jam Dipanggil' => $row->called_at?->timezone($tz)->format('H:i:s') ?? '-',
            'Jam Mulai Dilayani' => $row->served_at?->timezone($tz)->format('H:i:s') ?? '-',
            'Jam Selesai' => $row->finished_at?->timezone($tz)->format('H:i:s') ?? '-',
            'Waktu Tunggu' => $row->formatDuration($row->waitingSeconds()),
            'Waktu Layanan' => $row->formatDuration($row->serviceSeconds()),
            'Petugas Panggil' => $row->calledBy?->name ?? '-',
            'Petugas Layani' => $row->servedBy?->name ?? $row->finishedBy?->name ?? '-',
            'Catatan Admin' => $row->admin_note ?? '-',
        ];
    }

    public function applySurveyFilters(Builder $query, Request $request): Builder
    {
        $this->filters->applyDateFilter($query, $request, 'created_at');

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->filled('satisfaction_level')) {
            $level = AcademicSatisfactionSurvey::normalizeLevel($request->satisfaction_level);
            $query->where(function (Builder $q) use ($level, $request) {
                $q->where('satisfaction_level', $level)
                    ->orWhere('satisfaction_level', $request->satisfaction_level)
                    ->orWhereRaw('LOWER(REPLACE(satisfaction_level, " ", "_")) = ?', [$level]);
            });
        }

        if ($request->filled('reason')) {
            $query->where('reason', 'like', '%'.$request->reason.'%');
        }

        if ($request->filled('respondent_name')) {
            $query->where('respondent_name', 'like', '%'.$request->respondent_name.'%');
        }

        if ($request->filled('respondent_email')) {
            $query->where('respondent_email', 'like', '%'.$request->respondent_email.'%');
        }

        if ($request->filled('ticket_number')) {
            $query->where('ticket_number', 'like', '%'.$request->ticket_number.'%');
        }

        $user = $request->user();
        if ($user?->isAdminJurusan() && $user->study_program_id) {
            $query->where(function (Builder $q) use ($user) {
                $q->whereHas('submission', fn ($s) => $s->where('study_program_id', $user->study_program_id))
                    ->orWhereNull('submission_id');
            });
        }

        return $query;
    }

    public function surveyQuery(Request $request): Builder
    {
        return $this->applySurveyFilters(AcademicSatisfactionSurvey::query(), $request)
            ->with(['serviceType', 'submission.studyProgram'])
            ->latest();
    }

    public function surveyReportPayload(Request $request, bool $paginate = true): array
    {
        $period = $this->filters->resolvePeriod($request);

        $query = $this->surveyQuery($request);
        $rows = $paginate
            ? $query->paginate(25)->withQueryString()
            : $query->get();

        $all = $paginate ? $this->surveyQuery($request)->get() : $rows;
        $total = $all->count();
        $puas = $all->filter(fn ($r) => AcademicSatisfactionSurvey::normalizeLevel($r->satisfaction_level) === 'puas')->count();
        $cukup = $all->filter(fn ($r) => AcademicSatisfactionSurvey::normalizeLevel($r->satisfaction_level) === 'cukup_puas')->count();
        $kurang = $all->filter(fn ($r) => AcademicSatisfactionSurvey::normalizeLevel($r->satisfaction_level) === 'kurang_puas')->count();
        $pct = fn (int $value): float => $total > 0 ? round(($value / $total) * 100, 1) : 0.0;

        $topService = $all->groupBy(fn ($r) => $r->serviceDisplayName())->map->count()->sortDesc();
        $kurangByService = $all
            ->filter(fn ($r) => AcademicSatisfactionSurvey::normalizeLevel($r->satisfaction_level) === 'kurang_puas')
            ->groupBy(fn ($r) => $r->serviceDisplayName())
            ->map->count()
            ->sortDesc();

        $byReason = $all->groupBy(fn ($r) => $r->reason ?: 'Tidak diisi')
            ->map(fn (Collection $group, $label) => [
                'label' => $label,
                'total' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(10);

        $byService = $topService->map(fn ($count, $label) => [
            'label' => $label,
            'total' => $count,
        ])->values()->take(10);

        $summary = [
            'total' => $total,
            'puas' => $puas,
            'cukup_puas' => $cukup,
            'kurang_puas' => $kurang,
            'pct_puas' => $pct($puas),
            'pct_cukup_puas' => $pct($cukup),
            'pct_kurang_puas' => $pct($kurang),
            'top_service' => $topService->keys()->first() ?: '-',
            'top_kurang_service' => $kurangByService->keys()->first() ?: '-',
        ];

        return [
            'filters' => array_merge($period, $request->except(['page'])),
            'period_label' => $this->filters->periodLabel($period),
            'rows' => $rows,
            'summary' => $summary,
            'charts' => [
                'by_level' => [
                    ['label' => 'PUAS', 'total' => $summary['puas']],
                    ['label' => 'CUKUP PUAS', 'total' => $summary['cukup_puas']],
                    ['label' => 'KURANG PUAS', 'total' => $summary['kurang_puas']],
                ],
                'by_service' => $byService,
                'by_reason' => $byReason,
            ],
            'options' => $this->filterOptions($request->user()),
        ];
    }

    public function mapSurveyRow(AcademicSatisfactionSurvey $row, int $no): array
    {
        $tz = ReportFilterService::TIMEZONE;

        return [
            'No' => $no,
            'Tanggal Isi Survei' => $row->created_at?->timezone($tz)->format('d/m/Y H:i') ?? '-',
            'Nomor Tiket' => $row->ticket_number ?? '-',
            'Jenis Layanan' => $row->serviceDisplayName(),
            'Nama Responden' => $row->respondent_name ?? '-',
            'Email Responden' => $row->respondent_email ?? '-',
            'Tingkat Kepuasan' => $row->satisfactionLabel(),
            'Alasan' => $row->reason ?? '-',
            'Saran/Masukan' => $row->feedback ?? '-',
            'IP Address' => $row->ip_address ?? '-',
            'User Agent' => $row->user_agent ?? '-',
        ];
    }

    public function combinedReportPayload(Request $request): array
    {
        $period = $this->filters->resolvePeriod($request);
        $submissionPayload = $this->submissionReportPayload($request, false);
        $queuePayload = $this->queueReportPayload($request, false);
        $surveyPayload = $this->surveyReportPayload($request, false);

        $unfinished = collect($submissionPayload['rows'])
            ->filter(fn (Submission $row) => ! in_array($row->status, [
                SubmissionStatus::Completed->value,
                SubmissionStatus::EmailSent->value,
                SubmissionStatus::Rejected->value,
            ], true))
            ->values();

        $feedback = collect($surveyPayload['rows'])
            ->filter(fn ($row) => filled($row->feedback))
            ->take(50)
            ->values();

        $queueByDay = $queuePayload['charts']['by_day'] ?? collect();

        return [
            'filters' => array_merge($period, $request->except(['page'])),
            'period_label' => $this->filters->periodLabel($period),
            'submission_summary' => $submissionPayload['summary'],
            'queue_summary' => $queuePayload['summary'],
            'survey_summary' => $surveyPayload['summary'],
            'top_services' => $submissionPayload['charts']['by_service']->take(10),
            'submission_status_chart' => $submissionPayload['charts']['by_status'],
            'email_sent' => $submissionPayload['summary']['email_sent'],
            'email_not_sent' => max(0, $submissionPayload['summary']['total'] - $submissionPayload['summary']['email_sent']),
            'queue_by_day' => $queueByDay,
            'important_feedback' => $feedback,
            'unfinished' => $unfinished,
            'options' => $this->filterOptions($request->user()),
            'submission_rows' => $submissionPayload['rows'],
            'queue_rows' => $queuePayload['rows'],
            'survey_rows' => $surveyPayload['rows'],
        ];
    }

    public function submissionHeaders(): array
    {
        return [
            'No', 'Tanggal Pengajuan', 'Nomor Tiket', 'Jenis Layanan', 'Status Pengajuan', 'Nama Pengaju', 'NIM', 'NIK',
            'Program Studi', 'Semester', 'Email Pengaju', 'Nomor HP/Telepon', 'Keterangan/Keperluan Surat',
            'Tanggal Diproses', 'Tanggal Selesai', 'Admin Pemroses', 'Catatan Admin', 'File Final Ada/Tidak',
            'Tanggal Upload File Final', 'Email Terkirim', 'Tanggal Email Terkirim', 'Error Email', 'Link Progres',
            'Survei Kepuasan', 'Tingkat Kepuasan', 'Alasan Kepuasan', 'Saran/Masukan',
            'Verifikasi ADAK Jurusan', 'Tanggal Verifikasi Jurusan', 'Verifikator Jurusan', 'Catatan Verifikasi Jurusan',
            'Verifikasi Admin Pusat', 'Tanggal Verifikasi Pusat', 'Verifikator Pusat', 'Catatan Verifikasi Pusat',
            'Nama Kegiatan', 'Tanggal Kegiatan', 'Lokasi Kegiatan', 'Daftar Peserta', 'Status Sebelumnya', 'Alasan Pengaktifan Kembali',
        ];
    }

    public function queueHeaders(): array
    {
        return [
            'No', 'Tanggal', 'Nomor Antrean', 'Nama Pengunjung', 'Identitas', 'Nomor HP/Telepon', 'Jenis Pengunjung',
            'Keperluan Layanan', 'Status', 'Jam Ambil Nomor', 'Jam Dipanggil', 'Jam Mulai Dilayani', 'Jam Selesai',
            'Waktu Tunggu', 'Waktu Layanan', 'Petugas Panggil', 'Petugas Layani', 'Catatan Admin',
        ];
    }

    public function surveyHeaders(): array
    {
        return [
            'No', 'Tanggal Isi Survei', 'Nomor Tiket', 'Jenis Layanan', 'Nama Responden', 'Email Responden',
            'Tingkat Kepuasan', 'Alasan', 'Saran/Masukan', 'IP Address', 'User Agent',
        ];
    }

    private function baseSubmissionQuery(Request $request): Builder
    {
        $query = Submission::query();
        $user = $request->user();
        if ($user?->isAdminJurusan() && $user->study_program_id) {
            $query->where('study_program_id', $user->study_program_id);
        }

        return $query;
    }

    private function requestWithPeriod(Request $request, array $period): Request
    {
        $cloned = Request::create($request->url(), 'GET', array_merge($request->all(), [
            'date_from' => $period['start_date'],
            'date_to' => $period['end_date'],
            'start_date' => $period['start_date'],
            'end_date' => $period['end_date'],
            'period' => $period['period'],
        ]));
        $cloned->setUserResolver(fn () => $request->user());

        return $cloned;
    }

    private function adaptQueueRequest(Request $request): Request
    {
        $period = $this->filters->resolvePeriod($request);
        $legacyPeriod = match ($period['period']) {
            'today' => 'today',
            'this_week' => 'week',
            'this_month' => 'month',
            default => 'custom',
        };

        $cloned = Request::create($request->url(), 'GET', array_merge($request->all(), [
            'period' => $legacyPeriod,
            'date_from' => $period['start_date'],
            'date_to' => $period['end_date'],
        ]));
        $cloned->setUserResolver(fn () => $request->user());

        return $cloned;
    }
}
