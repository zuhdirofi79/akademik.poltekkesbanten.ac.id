<?php

namespace App\Services;

use App\Models\AcademicServiceQueue;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademicQueueService
{
    public function today(): string
    {
        return now('Asia/Jakarta')->toDateString();
    }

    public function nowJakarta(): Carbon
    {
        return now('Asia/Jakarta');
    }

    public function purposeOptions(): Collection
    {
        $services = ServiceType::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name']);

        $extras = collect(AcademicServiceQueue::EXTRA_PURPOSES)->map(fn ($name) => [
            'id' => null,
            'name' => $name,
            'value' => 'extra:'.$name,
        ]);

        return $services->map(fn (ServiceType $service) => [
            'id' => $service->id,
            'name' => $service->name,
            'value' => 'service:'.$service->id,
        ])->concat($extras)->values();
    }

    public function takeQueue(array $data): AcademicServiceQueue
    {
        $attempts = 0;

        while ($attempts < 3) {
            $attempts++;

            try {
                return DB::transaction(function () use ($data) {
                    $today = $this->today();

                    AcademicServiceQueue::whereDate('queue_date', $today)->lockForUpdate()->get();

                    $lastNumber = AcademicServiceQueue::whereDate('queue_date', $today)->max('queue_number');
                    $nextNumber = ((int) $lastNumber) + 1;
                    $queueCode = 'A-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

                    [$serviceTypeId, $serviceName, $purpose] = $this->resolvePurpose($data['purpose'] ?? null);

                    return AcademicServiceQueue::create([
                        'queue_date' => $today,
                        'queue_number' => $nextNumber,
                        'queue_code' => $queueCode,
                        'visitor_name' => $data['visitor_name'] ?? null,
                        'visitor_identity' => $data['visitor_identity'] ?? null,
                        'visitor_phone' => $data['visitor_phone'] ?? null,
                        'visitor_type' => $data['visitor_type'],
                        'service_type_id' => $serviceTypeId,
                        'service_name' => $serviceName,
                        'purpose' => $purpose,
                        'status' => AcademicServiceQueue::STATUS_WAITING,
                    ]);
                });
            } catch (UniqueConstraintViolationException|QueryException $e) {
                if ($attempts >= 3 || ! $this->isUniqueViolation($e)) {
                    throw $e;
                }
            }
        }

        throw ValidationException::withMessages([
            'visitor_type' => 'Gagal membuat nomor antrean. Silakan coba lagi.',
        ]);
    }

    public function waitingAheadCount(AcademicServiceQueue $queue): int
    {
        return AcademicServiceQueue::query()
            ->whereDate('queue_date', $queue->queue_date->toDateString())
            ->where('status', AcademicServiceQueue::STATUS_WAITING)
            ->where('queue_number', '<', $queue->queue_number)
            ->count();
    }

    public function publicStatusPayload(): array
    {
        $today = $this->today();
        $now = $this->nowJakarta();

        $current = AcademicServiceQueue::query()
            ->whereDate('queue_date', $today)
            ->whereIn('status', [
                AcademicServiceQueue::STATUS_CALLED,
                AcademicServiceQueue::STATUS_SERVING,
            ])
            ->orderByDesc('called_at')
            ->orderByDesc('id')
            ->first();

        $waiting = AcademicServiceQueue::query()
            ->whereDate('queue_date', $today)
            ->where('status', AcademicServiceQueue::STATUS_WAITING)
            ->orderBy('queue_number')
            ->limit(5)
            ->get();

        $recentDone = AcademicServiceQueue::query()
            ->whereDate('queue_date', $today)
            ->whereIn('status', [
                AcademicServiceQueue::STATUS_SERVED,
                AcademicServiceQueue::STATUS_SKIPPED,
            ])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return [
            'date' => $now->translatedFormat('l, d F Y'),
            'date_iso' => $today,
            'time' => $now->format('H:i:s'),
            'current' => $current?->toPublicArray(),
            'waiting' => $waiting->map->toPublicArray()->values()->all(),
            'recent_done' => $recentDone->map->toPublicArray()->values()->all(),
            'served_count' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_SERVED),
            'waiting_count' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_WAITING),
            'skipped_count' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_SKIPPED),
            'called_count' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_CALLED),
            'serving_count' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_SERVING),
            'cancelled_count' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_CANCELLED),
            'total_today' => AcademicServiceQueue::whereDate('queue_date', $today)->count(),
        ];
    }

    public function adminStatusPayload(): array
    {
        $today = $this->today();
        $public = $this->publicStatusPayload();

        $groups = [
            'waiting' => AcademicServiceQueue::STATUS_WAITING,
            'called' => AcademicServiceQueue::STATUS_CALLED,
            'serving' => AcademicServiceQueue::STATUS_SERVING,
            'served' => AcademicServiceQueue::STATUS_SERVED,
            'skipped' => AcademicServiceQueue::STATUS_SKIPPED,
            'cancelled' => AcademicServiceQueue::STATUS_CANCELLED,
        ];

        $lists = [];
        foreach ($groups as $key => $status) {
            $query = AcademicServiceQueue::query()
                ->with(['calledBy', 'servedBy', 'finishedBy'])
                ->whereDate('queue_date', $today)
                ->where('status', $status);

            if ($status === AcademicServiceQueue::STATUS_WAITING) {
                $query->orderBy('queue_number');
            } else {
                $query->orderByDesc('updated_at')->orderByDesc('id');
            }

            $lists[$key] = $query->get()
                ->map(fn (AcademicServiceQueue $queue) => $this->adminQueueArray($queue))
                ->values()
                ->all();
        }

        return array_merge($public, [
            'lists' => $lists,
        ]);
    }

    public function dashboardStats(): array
    {
        $today = $this->today();

        return [
            'total_today' => AcademicServiceQueue::whereDate('queue_date', $today)->count(),
            'waiting' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_WAITING),
            'active' => AcademicServiceQueue::whereDate('queue_date', $today)
                ->whereIn('status', [
                    AcademicServiceQueue::STATUS_CALLED,
                    AcademicServiceQueue::STATUS_SERVING,
                ])->count(),
            'served' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_SERVED),
            'skipped' => $this->countTodayByStatus(AcademicServiceQueue::STATUS_SKIPPED),
        ];
    }

    public function callNext(?User $user = null): ?AcademicServiceQueue
    {
        return DB::transaction(function () use ($user) {
            $today = $this->today();

            $next = AcademicServiceQueue::query()
                ->whereDate('queue_date', $today)
                ->where('status', AcademicServiceQueue::STATUS_WAITING)
                ->orderBy('queue_number')
                ->lockForUpdate()
                ->first();

            if (! $next) {
                return null;
            }

            return $this->markCalled($next, $user);
        });
    }

    public function call(AcademicServiceQueue $queue, ?User $user = null): AcademicServiceQueue
    {
        return DB::transaction(function () use ($queue, $user) {
            $fresh = AcademicServiceQueue::whereKey($queue->id)->lockForUpdate()->firstOrFail();

            if (! in_array($fresh->status, [
                AcademicServiceQueue::STATUS_WAITING,
                AcademicServiceQueue::STATUS_SKIPPED,
                AcademicServiceQueue::STATUS_CALLED,
            ], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Antrean tidak dapat dipanggil dari status saat ini.',
                ]);
            }

            return $this->markCalled($fresh, $user);
        });
    }

    public function serve(AcademicServiceQueue $queue, ?User $user = null): AcademicServiceQueue
    {
        return DB::transaction(function () use ($queue, $user) {
            $fresh = AcademicServiceQueue::whereKey($queue->id)->lockForUpdate()->firstOrFail();

            if (! in_array($fresh->status, [
                AcademicServiceQueue::STATUS_CALLED,
                AcademicServiceQueue::STATUS_WAITING,
            ], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Antrean tidak dapat mulai dilayani dari status saat ini.',
                ]);
            }

            $fresh->update([
                'status' => AcademicServiceQueue::STATUS_SERVING,
                'served_at' => now(),
                'served_by' => $user?->id ?? auth()->id(),
                'called_at' => $fresh->called_at ?? now(),
                'called_by' => $fresh->called_by ?? ($user?->id ?? auth()->id()),
            ]);

            return $fresh->fresh();
        });
    }

    public function finish(AcademicServiceQueue $queue, ?User $user = null, ?string $note = null): AcademicServiceQueue
    {
        return DB::transaction(function () use ($queue, $user, $note) {
            $fresh = AcademicServiceQueue::whereKey($queue->id)->lockForUpdate()->firstOrFail();

            if (! in_array($fresh->status, [
                AcademicServiceQueue::STATUS_CALLED,
                AcademicServiceQueue::STATUS_SERVING,
            ], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Antrean tidak dapat diselesaikan dari status saat ini.',
                ]);
            }

            $payload = [
                'status' => AcademicServiceQueue::STATUS_SERVED,
                'finished_at' => now(),
                'finished_by' => $user?->id ?? auth()->id(),
            ];

            if ($note !== null && $note !== '') {
                $payload['admin_note'] = $note;
            }

            if (! $fresh->served_at) {
                $payload['served_at'] = now();
                $payload['served_by'] = $user?->id ?? auth()->id();
            }

            $fresh->update($payload);

            return $fresh->fresh();
        });
    }

    public function skip(AcademicServiceQueue $queue, ?User $user = null, ?string $note = null): AcademicServiceQueue
    {
        return DB::transaction(function () use ($queue, $user, $note) {
            $fresh = AcademicServiceQueue::whereKey($queue->id)->lockForUpdate()->firstOrFail();

            if (! in_array($fresh->status, [
                AcademicServiceQueue::STATUS_WAITING,
                AcademicServiceQueue::STATUS_CALLED,
                AcademicServiceQueue::STATUS_SERVING,
            ], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Antrean tidak dapat dilewati dari status saat ini.',
                ]);
            }

            $payload = [
                'status' => AcademicServiceQueue::STATUS_SKIPPED,
                'skipped_at' => now(),
                'skipped_by' => $user?->id ?? auth()->id(),
            ];

            if ($note !== null && $note !== '') {
                $payload['admin_note'] = $note;
            }

            $fresh->update($payload);

            return $fresh->fresh();
        });
    }

    public function cancel(AcademicServiceQueue $queue, ?User $user = null, ?string $note = null): AcademicServiceQueue
    {
        return DB::transaction(function () use ($queue, $user, $note) {
            $fresh = AcademicServiceQueue::whereKey($queue->id)->lockForUpdate()->firstOrFail();

            if (in_array($fresh->status, [
                AcademicServiceQueue::STATUS_SERVED,
                AcademicServiceQueue::STATUS_CANCELLED,
            ], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Antrean tidak dapat dibatalkan dari status saat ini.',
                ]);
            }

            $payload = [
                'status' => AcademicServiceQueue::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by' => $user?->id ?? auth()->id(),
            ];

            if ($note !== null && $note !== '') {
                $payload['admin_note'] = $note;
            }

            $fresh->update($payload);

            return $fresh->fresh();
        });
    }

    public function updateNote(AcademicServiceQueue $queue, ?string $note): AcademicServiceQueue
    {
        $queue->update(['admin_note' => $note]);

        return $queue->fresh();
    }

    private function markCalled(AcademicServiceQueue $queue, ?User $user = null): AcademicServiceQueue
    {
        $queue->update([
            'status' => AcademicServiceQueue::STATUS_CALLED,
            'called_at' => now(),
            'called_by' => $user?->id ?? auth()->id(),
            'skipped_at' => null,
            'skipped_by' => null,
        ]);

        return $queue->fresh();
    }

    private function countTodayByStatus(string $status): int
    {
        return AcademicServiceQueue::query()
            ->whereDate('queue_date', $this->today())
            ->where('status', $status)
            ->count();
    }

    private function resolvePurpose(?string $purposeValue): array
    {
        if (! $purposeValue) {
            return [null, null, null];
        }

        if (str_starts_with($purposeValue, 'service:')) {
            $id = (int) substr($purposeValue, 8);
            $service = ServiceType::active()->find($id);
            if (! $service) {
                return [null, null, null];
            }

            return [$service->id, $service->name, $service->name];
        }

        if (str_starts_with($purposeValue, 'extra:')) {
            $name = substr($purposeValue, 6);

            return [null, $name, $name];
        }

        return [null, $purposeValue, $purposeValue];
    }

    private function adminQueueArray(AcademicServiceQueue $queue): array
    {
        return array_merge($queue->toPublicArray(), [
            'visitor_identity' => $queue->visitor_identity,
            'visitor_phone' => $queue->visitor_phone,
            'admin_note' => $queue->admin_note,
            'served_at' => $queue->served_at?->timezone('Asia/Jakarta')->format('H:i:s'),
            'finished_at' => $queue->finished_at?->timezone('Asia/Jakarta')->format('H:i:s'),
            'officer' => $queue->officerName(),
            'actions' => $this->availableActions($queue),
        ]);
    }

    private function availableActions(AcademicServiceQueue $queue): array
    {
        return match ($queue->status) {
            AcademicServiceQueue::STATUS_WAITING => ['call', 'skip', 'cancel', 'note'],
            AcademicServiceQueue::STATUS_CALLED => ['serve', 'finish', 'skip', 'recall', 'note'],
            AcademicServiceQueue::STATUS_SERVING => ['finish', 'skip', 'note'],
            AcademicServiceQueue::STATUS_SKIPPED => ['recall', 'note'],
            default => ['note'],
        };
    }

    private function isUniqueViolation(QueryException $e): bool
    {
        if ($e instanceof UniqueConstraintViolationException) {
            return true;
        }

        $code = (string) ($e->errorInfo[1] ?? '');

        return in_array($code, ['1062', '19'], true)
            || str_contains(strtolower($e->getMessage()), 'unique');
    }
}
