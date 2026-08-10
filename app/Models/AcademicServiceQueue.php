<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicServiceQueue extends Model
{
    public const STATUS_WAITING = 'waiting';

    public const STATUS_CALLED = 'called';

    public const STATUS_SERVING = 'serving';

    public const STATUS_SERVED = 'served';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_CANCELLED = 'cancelled';

    public const VISITOR_TYPES = [
        'Mahasiswa',
        'Alumni',
        'Umum',
        'Pegawai',
    ];

    public const EXTRA_PURPOSES = [
        'Konsultasi Layanan Akademik',
        'Pengambilan/Penyerahan Berkas',
        'Lainnya',
    ];

    protected $fillable = [
        'queue_date',
        'queue_number',
        'queue_code',
        'visitor_name',
        'visitor_identity',
        'visitor_phone',
        'visitor_type',
        'service_type_id',
        'service_name',
        'purpose',
        'status',
        'called_at',
        'served_at',
        'skipped_at',
        'cancelled_at',
        'finished_at',
        'called_by',
        'served_by',
        'skipped_by',
        'cancelled_by',
        'finished_by',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'called_at' => 'datetime',
            'served_at' => 'datetime',
            'skipped_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function calledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'called_by');
    }

    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function skippedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'skipped_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function finishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finished_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_WAITING => 'Menunggu',
            self::STATUS_CALLED => 'Sedang Dipanggil',
            self::STATUS_SERVING => 'Sedang Dilayani',
            self::STATUS_SERVED => 'Selesai Dilayani',
            self::STATUS_SKIPPED => 'Dilewati',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    public function purposeDisplay(): string
    {
        return $this->purpose
            ?: ($this->service_name
                ?: ($this->serviceType?->name ?: '-'));
    }

    public function waitingSeconds(): ?int
    {
        if (! $this->called_at || ! $this->created_at) {
            return null;
        }

        return max(0, $this->created_at->diffInSeconds($this->called_at));
    }

    public function serviceSeconds(): ?int
    {
        if (! $this->finished_at) {
            return null;
        }

        $start = $this->served_at ?? $this->called_at;
        if (! $start) {
            return null;
        }

        return max(0, $start->diffInSeconds($this->finished_at));
    }

    public function formatDuration(?int $seconds): string
    {
        if ($seconds === null) {
            return '-';
        }

        $minutes = intdiv($seconds, 60);
        $remain = $seconds % 60;

        if ($minutes <= 0) {
            return $remain.' dtk';
        }

        return $minutes.' mnt '.$remain.' dtk';
    }

    public function officerName(): string
    {
        return $this->finishedBy?->name
            ?? $this->servedBy?->name
            ?? $this->calledBy?->name
            ?? $this->skippedBy?->name
            ?? $this->cancelledBy?->name
            ?? '-';
    }

    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'queue_code' => $this->queue_code,
            'queue_number' => $this->queue_number,
            'visitor_name' => $this->visitor_name,
            'visitor_type' => $this->visitor_type,
            'purpose' => $this->purposeDisplay(),
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'called_at' => $this->called_at?->timezone('Asia/Jakarta')->format('H:i:s'),
            'created_at' => $this->created_at?->timezone('Asia/Jakarta')->format('H:i:s'),
        ];
    }
}
