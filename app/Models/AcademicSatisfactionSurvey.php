<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicSatisfactionSurvey extends Model
{
    protected $fillable = [
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

    public function satisfactionLabel(): string
    {
        return match ($this->satisfaction_level) {
            'puas' => 'PUAS',
            'cukup_puas' => 'CUKUP PUAS',
            'kurang_puas' => 'KURANG PUAS',
            default => strtoupper(str_replace('_', ' ', (string) $this->satisfaction_level)),
        };
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
}
