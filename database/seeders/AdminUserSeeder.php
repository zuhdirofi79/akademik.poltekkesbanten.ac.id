<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\RolePriority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $forceReset = filter_var(env('FORCE_RESET_ADMIN_PASSWORD', false), FILTER_VALIDATE_BOOLEAN);

        $sources = [
            ['email' => env('SUPERADMIN_EMAIL'), 'password' => env('SUPERADMIN_PASSWORD'), 'role' => 'superadmin', 'label' => 'SUPERADMIN_EMAIL'],
            ['email' => env('SUPERADMIN_1_EMAIL'), 'password' => env('SUPERADMIN_1_PASSWORD'), 'role' => 'superadmin', 'label' => 'SUPERADMIN_1_EMAIL'],
            ['email' => env('DEFAULT_ADMIN_EMAIL'), 'password' => env('DEFAULT_ADMIN_PASSWORD'), 'role' => 'admin', 'label' => 'DEFAULT_ADMIN_EMAIL'],
        ];

        $candidates = [];

        foreach ($sources as $source) {
            $email = strtolower(trim((string) $source['email']));
            $password = (string) $source['password'];

            if ($email === '' || $password === '') {
                $this->command?->warn("Lewati {$source['label']}: email atau password env belum diisi.");

                continue;
            }

            if (! isset($candidates[$email])) {
                $candidates[$email] = [
                    'email' => $email,
                    'password' => $password,
                    'role' => $source['role'],
                ];

                continue;
            }

            $existing = $candidates[$email];
            $higherRole = RolePriority::highest($source['role'], $existing['role']);

            $candidates[$email]['role'] = $higherRole;

            if (RolePriority::isHigher($source['role'], $existing['role'])) {
                $candidates[$email]['password'] = $password;
            }
        }

        if ($candidates === []) {
            throw new RuntimeException('Seeder gagal: tidak ada konfigurasi admin/superadmin yang valid di .env.');
        }

        foreach ($candidates as $candidate) {
            $this->upsertUser($candidate, $forceReset);
        }

        $activeSuperadmins = User::where('role', 'superadmin')->where('is_active', true)->count();

        if ($activeSuperadmins < 1) {
            throw new RuntimeException('Seeder gagal: minimal harus ada satu superadmin aktif.');
        }

        $this->command?->info('AdminUserSeeder selesai. Total akun unik: '.count($candidates).", superadmin aktif: {$activeSuperadmins}.");
    }

    private function upsertUser(array $candidate, bool $forceReset): void
    {
        $existing = User::where('email', $candidate['email'])->first();

        $attributes = [
            'name' => $this->nameFromEmail($candidate['email']),
            'role' => $existing
                ? RolePriority::highest($candidate['role'], $existing->role)
                : $candidate['role'],
            'is_active' => true,
        ];

        $shouldSetPassword = ! $existing || $forceReset;

        if ($shouldSetPassword) {
            $attributes['password'] = Hash::make($candidate['password']);
            $attributes['must_change_password'] = true;
        } elseif ($existing && RolePriority::isHigher($candidate['role'], $existing->role)) {
            $attributes['role'] = $candidate['role'];
        }

        User::updateOrCreate(
            ['email' => $candidate['email']],
            $attributes
        );
    }

    private function nameFromEmail(string $email): string
    {
        $local = explode('@', $email)[0] ?? $email;
        $name = str_replace(['.', '_', '-'], ' ', $local);

        return ucwords($name);
    }
}
