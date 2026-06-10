<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\RolePriority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $forceReset = filter_var(env('FORCE_RESET_ADMIN_PASSWORD', false), FILTER_VALIDATE_BOOLEAN);

        $sources = [
            ['email' => env('SUPERADMIN_EMAIL'), 'password' => env('SUPERADMIN_PASSWORD'), 'role' => 'superadmin', 'label' => 'SUPERADMIN_EMAIL'],
            ['email' => env('SUPERADMIN_1_EMAIL'), 'password' => env('SUPERADMIN_1_PASSWORD'), 'role' => 'superadmin', 'label' => 'SUPERADMIN_1_EMAIL'],
            ['email' => env('SUPERADMIN_2_EMAIL'), 'password' => env('SUPERADMIN_2_PASSWORD'), 'role' => 'superadmin', 'label' => 'SUPERADMIN_2_EMAIL'],
            ['email' => env('SUPERADMIN_3_EMAIL'), 'password' => env('SUPERADMIN_3_PASSWORD'), 'role' => 'superadmin', 'label' => 'SUPERADMIN_3_EMAIL'],
            ['email' => env('DEFAULT_ADMIN_EMAIL'), 'password' => env('DEFAULT_ADMIN_PASSWORD'), 'role' => 'admin', 'label' => 'DEFAULT_ADMIN_EMAIL'],
        ];

        $candidates = [];
        $duplicateNotes = [];

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
                    'labels' => [$source['label']],
                ];

                continue;
            }

            $existing = $candidates[$email];
            $higherRole = RolePriority::highest($source['role'], $existing['role']);

            if ($existing['role'] === 'superadmin' || $higherRole === 'superadmin') {
                $duplicateNotes[] = "{$email} ({$source['label']} duplikat, dipertahankan sebagai superadmin)";
            } else {
                $duplicateNotes[] = "{$email} ({$source['label']} duplikat, digabung ke akun yang sama)";
            }

            $candidates[$email]['role'] = $higherRole;
            $candidates[$email]['labels'][] = $source['label'];

            if (RolePriority::isHigher($source['role'], $existing['role'])) {
                $candidates[$email]['password'] = $password;
            }
        }

        if ($candidates === []) {
            throw new RuntimeException('Seeder gagal: tidak ada konfigurasi admin/superadmin yang valid di .env.');
        }

        $processedEmails = [];

        foreach ($candidates as $candidate) {
            $this->upsertUser($candidate, $forceReset);
            $processedEmails[] = $candidate['email'];
        }

        $activeSuperadmins = User::where('role', 'superadmin')->where('is_active', true)->count();

        if ($activeSuperadmins < 1) {
            throw new RuntimeException('Seeder gagal: minimal harus ada satu superadmin aktif.');
        }

        $this->command?->info('AdminUserSeeder selesai.');
        $this->command?->info('Total akun unik diproses: '.count($candidates));
        $this->command?->info("Total superadmin aktif: {$activeSuperadmins}");
        $this->command?->info('Email dibuat/diupdate: '.implode(', ', $processedEmails));

        foreach ($duplicateNotes as $note) {
            $this->command?->line("  Duplikat: {$note}");
        }
    }

    private function upsertUser(array $candidate, bool $forceReset): void
    {
        $user = User::firstOrNew([
            'email' => $candidate['email'],
        ]);

        $isNew = ! $user->exists;

        $user->name = $this->nameFromEmail($candidate['email']);
        $user->role = $isNew
            ? $candidate['role']
            : RolePriority::highest($candidate['role'], (string) $user->role);
        $user->is_active = true;

        if ($isNew || $forceReset) {
            $user->password = Hash::make($candidate['password']);

            if (Schema::hasColumn($user->getTable(), 'must_change_password')) {
                $user->must_change_password = true;
            }
        }

        $user->save();
    }

    private function nameFromEmail(string $email): string
    {
        $local = explode('@', $email)[0] ?? $email;
        $name = str_replace(['.', '_', '-'], ' ', $local);

        return ucwords($name);
    }
}
