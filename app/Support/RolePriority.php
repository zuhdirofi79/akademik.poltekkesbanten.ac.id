<?php

namespace App\Support;

class RolePriority
{
    private const PRIORITIES = [
        'admin_jurusan' => 1,
        'admin' => 2,
        'superadmin' => 3,
    ];

    public static function value(string $role): int
    {
        return self::PRIORITIES[$role] ?? 0;
    }

    public static function isHigher(string $role, string $compareTo): bool
    {
        return self::value($role) > self::value($compareTo);
    }

    public static function highest(string $roleA, string $roleB): string
    {
        return self::value($roleA) >= self::value($roleB) ? $roleA : $roleB;
    }
}
