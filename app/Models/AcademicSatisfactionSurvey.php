<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicSatisfactionSurvey extends Model
{
    protected $fillable = [
        'submission_id',
        'satisfaction_level',
        'service_type_id',
        'service_name',
        'reason',
        'feedback',
        'respondent_name',
        'respondent_email',
        'ticket_number',
        'ip_address',
        'user_agent',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public static function normalizeLevel(?string $level): string
    {
        $value = strtolower(trim((string) $level));
        $value = str_replace([' ', '-'], '_', $value);
        $value = preg_replace('/_+/', '_', $value) ?? $value;

        return match ($value) {
            'puas' => 'puas',
            'cukup_puas', 'cukuppuas' => 'cukup_puas',
            'kurang_puas', 'kurangpuas' => 'kurang_puas',
            default => $value,
        };
    }

    public function satisfactionLabel(): string
    {
        return match (self::normalizeLevel($this->satisfaction_level)) {
            'puas' => 'PUAS',
            'cukup_puas' => 'CUKUP PUAS',
            'kurang_puas' => 'KURANG PUAS',
            default => strtoupper(str_replace('_', ' ', (string) $this->satisfaction_level)),
        };
    }

    public function satisfactionBadgeClass(): string
    {
        return match (self::normalizeLevel($this->satisfaction_level)) {
            'puas' => 'survey-badge-puas',
            'cukup_puas' => 'survey-badge-cukup',
            'kurang_puas' => 'survey-badge-kurang',
            default => 'survey-badge-default',
        };
    }

    public function serviceDisplayName(): string
    {
        return $this->serviceType?->name
            ?? $this->service_name
            ?? 'Tidak disebutkan';
    }

    public static function reasonsByLevel(): array
    {
        return [
            'puas' => [
                'Layanan Cepat',
                'Petugas Ramah',
            ],
            'cukup_puas' => [
                'Layanan Cukup Cepat',
                'Petugas Cukup Ramah',
            ],
            'kurang_puas' => [
                'Pelayanan Lama',
                'Petugas Tidak Ramah',
            ],
        ];
    }

    public function scopeNormalizedLevel(Builder $query, string $level): Builder
    {
        $normalized = self::normalizeLevel($level);

        return $query->where(function (Builder $q) use ($normalized, $level) {
            $q->where('satisfaction_level', $normalized)
                ->orWhere('satisfaction_level', $level)
                ->orWhereRaw('LOWER(REPLACE(satisfaction_level, " ", "_")) = ?', [$normalized]);
        });
    }
}
