<?php

namespace Database\Seeders;

use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'D3 Keperawatan',
            'D3 Kebidanan',
            'D3 Teknologi Laboratorium Medis',
            'Sarjana Terapan Keperawatan',
            'Sarjana Terapan Kebidanan',
        ];

        foreach ($programs as $program) {
            StudyProgram::updateOrCreate(['name' => $program], ['is_active' => true]);
        }
    }
}
