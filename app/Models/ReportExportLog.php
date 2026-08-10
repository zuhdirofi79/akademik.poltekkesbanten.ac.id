<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExportLog extends Model
{
    protected $fillable = [
        'user_id',
        'report_type',
        'format',
        'filters',
        'file_name',
        'exported_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'exported_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
