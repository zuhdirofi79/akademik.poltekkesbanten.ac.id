<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'code' => 'submission_received',
                'name' => 'Pengajuan Diterima',
                'subject' => 'Pengajuan Layanan Akademik - {{tiket}}',
                'body' => 'Halo {{nama}}, pengajuan {{layanan}} dengan nomor tiket {{tiket}} telah kami terima. Simpan token progres Anda: {{token}}. Cek progres di {{link_progres}}.',
            ],
            [
                'code' => 'submission_completed',
                'name' => 'File Selesai',
                'subject' => 'File Layanan Akademik Tersedia - {{tiket}}',
                'body' => 'Halo {{nama}}, pengajuan {{layanan}} dengan nomor tiket {{tiket}} sudah selesai. Silakan cek progres di {{link_progres}}.',
            ],
            [
                'code' => 'submission_status_changed',
                'name' => 'Perubahan Status',
                'subject' => 'Update Status Pengajuan - {{tiket}}',
                'body' => 'Halo {{nama}}, status pengajuan {{layanan}} ({{tiket}}) berubah dari {{status_lama}} menjadi {{status_baru}}. Catatan: {{catatan}}. Cek progres di {{link_progres}}.',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['code' => $template['code']],
                array_merge($template, ['is_active' => true])
            );
        }
    }
}
