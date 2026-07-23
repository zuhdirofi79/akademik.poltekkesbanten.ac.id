<?php

namespace Database\Seeders;

use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class AdminJurusanSeeder extends Seeder
{
    public function run(): void
    {
        $forceReset = filter_var(env('FORCE_RESET_ADMIN_PASSWORD', false), FILTER_VALIDATE_BOOLEAN);
        $defaultPassword = (string) env('ADMIN_JURUSAN_DEFAULT_PASSWORD', '');

        $accounts = [
            [
                'name' => 'Admin D3 Kebidanan',
                'email' => env('ADMIN_D3_KEBIDANAN_EMAIL', 'poltekkesadakpoltekkes@gmail.com'),
                'password' => env('ADMIN_D3_KEBIDANAN_PASSWORD', $defaultPassword),
                'study_program' => 'D3 Kebidanan',
            ],
            [
                'name' => 'Admin D3 Keperawatan',
                'email' => env('ADMIN_D3_KEPERAWATAN_EMAIL', 'd3.keperawatan@poltekkesbanten.ac.id'),
                'password' => env('ADMIN_D3_KEPERAWATAN_PASSWORD', $defaultPassword),
                'study_program' => 'D3 Keperawatan',
            ],
            [
                'name' => 'Admin D3 TLM',
                'email' => env('ADMIN_D3_TLM_EMAIL', 'adak.tlm@poltekkesbanten.ac.id'),
                'password' => env('ADMIN_D3_TLM_PASSWORD', $defaultPassword),
                'study_program' => 'D3 Teknologi Laboratorium Medis',
            ],
            [
                'name' => 'Admin STR Ners',
                'email' => env('ADMIN_STR_NERS_EMAIL', 'strners@poltekkesbanten.ac.id'),
                'password' => env('ADMIN_STR_NERS_PASSWORD', $defaultPassword),
                'study_program' => 'Sarjana Terapan Keperawatan',
            ],
        ];

        $processed = [];

        foreach ($accounts as $account) {
            $email = strtolower(trim((string) $account['email']));
            $password = (string) $account['password'];

            if ($email === '' || $password === '') {
                $this->command?->warn("Lewati {$account['name']}: email atau password belum dikonfigurasi.");

                continue;
            }

            $studyProgram = StudyProgram::where('name', $account['study_program'])->first();

            if (! $studyProgram) {
                throw new RuntimeException(
                    "Program studi \"{$account['study_program']}\" tidak ditemukan. Jalankan StudyProgramSeeder terlebih dahulu."
                );
            }

            $user = User::firstOrNew(['email' => $email]);
            $isNew = ! $user->exists;

            if ($isNew) {
                $user->name = $account['name'];
                $user->role = 'admin_jurusan';
                $user->study_program_id = $studyProgram->id;
                $user->is_active = true;
                $user->password = Hash::make($password);

                if (Schema::hasColumn($user->getTable(), 'must_change_password')) {
                    $user->must_change_password = true;
                }

                $user->save();
                $processed[] = "{$email} (baru)";
            } else {
                $user->name = $account['name'];
                $user->role = 'admin_jurusan';
                $user->study_program_id = $studyProgram->id;
                $user->is_active = true;

                if ($forceReset) {
                    $user->password = Hash::make($password);

                    if (Schema::hasColumn($user->getTable(), 'must_change_password')) {
                        $user->must_change_password = true;
                    }
                }

                $user->save();
                $processed[] = "{$email} (diupdate)";
            }
        }

        if ($processed === []) {
            throw new RuntimeException(
                'AdminJurusanSeeder gagal: tidak ada akun admin jurusan yang valid. Isi ADMIN_JURUSAN_DEFAULT_PASSWORD atau password per akun di .env.'
            );
        }

        $this->command?->info('AdminJurusanSeeder selesai.');
        $this->command?->info('Akun diproses: '.implode(', ', $processed));
    }
}
