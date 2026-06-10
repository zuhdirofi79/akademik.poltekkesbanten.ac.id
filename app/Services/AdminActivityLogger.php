<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\User;

class AdminActivityLogger
{
    public function log(string $action, string $description, ?User $user = null): void
    {
        $actor = $user ?? auth()->user();

        AdminActivityLog::create([
            'user_id' => $actor?->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
