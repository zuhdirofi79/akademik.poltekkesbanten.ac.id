<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'code' => 'PDM',
                'name' => 'Perubahan Data Mahasiswa Aktif',
                'slug' => 'perubahan-data-mahasiswa-aktif',
                'description' => 'Untuk mengusulkan perbaikan data mahasiswa aktif seperti NIM, NIK, nama, tempat/tanggal lahir, dan nama ibu kandung.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'AKTIF_KULIAH',
                'name' => 'Surat Keterangan Aktif Kuliah',
                'slug' => 'surat-keterangan-aktif-kuliah',
                'description' => 'Untuk mengusulkan pembuatan Surat Keterangan Aktif Kuliah.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'CUTI',
                'name' => 'Surat Cuti Mahasiswa',
                'slug' => 'surat-cuti-mahasiswa',
                'description' => 'Untuk mengusulkan Surat Cuti Mahasiswa.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'KELUAR',
                'name' => 'Surat Keluar Mahasiswa',
                'slug' => 'surat-keluar-mahasiswa',
                'description' => 'Untuk mengusulkan Surat Keluar Mahasiswa.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'BEASISWA',
                'name' => 'Surat Keterangan Beasiswa',
                'slug' => 'surat-keterangan-beasiswa',
                'description' => 'Untuk mengusulkan pembuatan Surat Keterangan Beasiswa.',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'BERKELAKUAN_BAIK',
                'name' => 'Surat Keterangan Berkelakuan Baik',
                'slug' => 'surat-keterangan-berkelakuan-baik',
                'description' => 'Untuk mengusulkan pembuatan Surat Keterangan Berkelakuan Baik.',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'code' => 'VERIFIKASI_IJAZAH_ALUMNI',
                'name' => 'Surat Keterangan Verifikasi Ijazah Alumni',
                'slug' => 'surat-keterangan-verifikasi-ijazah-alumni',
                'description' => 'Untuk mengusulkan pembuatan Surat Keterangan Verifikasi Ijazah Alumni.',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'code' => 'REKOMENDASI',
                'name' => 'Surat Rekomendasi Mahasiswa/Alumni',
                'slug' => 'surat-rekomendasi',
                'description' => 'Untuk mengusulkan Surat Rekomendasi Mahasiswa/Alumni, termasuk keperluan magang dan keperluan lainnya.',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'code' => 'SIAKAD',
                'name' => 'Layanan Sistem Informasi Akademik (SIAKAD)',
                'slug' => 'siakad',
                'description' => 'Untuk melaporkan kendala SIAKAD seperti lupa password, login, KRS, dan KHS.',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'code' => 'SURAT_TUGAS_MAHASISWA',
                'name' => 'Pengajuan Surat Tugas Mahasiswa',
                'slug' => 'surat-tugas-mahasiswa',
                'description' => 'Untuk pengajuan Surat Tugas Mahasiswa bagi mahasiswa yang mengikuti kegiatan.',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'code' => 'NOTA_DINAS_DISPEN',
                'name' => 'Pengajuan Nota Dinas Dispen Kegiatan Mahasiswa',
                'slug' => 'nota-dinas-dispen-kegiatan-mahasiswa',
                'description' => 'Untuk pengajuan Nota Dinas Dispensasi kegiatan mahasiswa.',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'code' => 'PENGAKTIFAN_KEMBALI',
                'name' => 'Pengajuan Pengaktifan Kembali Mahasiswa Cuti/Keluar',
                'slug' => 'pengaktifan-kembali-mahasiswa',
                'description' => 'Untuk pengajuan pengaktifan kembali mahasiswa yang sebelumnya cuti atau keluar.',
                'is_active' => true,
                'sort_order' => 12,
            ],
        ];

        foreach ($services as $service) {
            $existing = ServiceType::where('code', $service['code'])
                ->orWhere('slug', $service['slug'])
                ->first();
        
            if ($existing) {
                $existing->update([
                    'code' => $service['code'],
                    'name' => $service['name'],
                    'slug' => $service['slug'],
                    'description' => $service['description'],
                    'is_active' => $service['is_active'],
                    'sort_order' => $service['sort_order'],
                ]);
            } else {
                ServiceType::create($service);
            }
        }

        $legacyServices = [
            [
                'code' => 'REGISTRASI',
                'name' => 'Surat Keterangan Registrasi',
                'slug' => 'surat-keterangan-registrasi',
                'description' => 'Layanan lama/nonaktif.',
                'is_active' => false,
                'sort_order' => 99,
            ],
            [
                'code' => 'PDM_LEGACY',
                'name' => 'Perbaikan Data Mahasiswa (Legacy)',
                'slug' => 'perbaikan-data-mahasiswa',
                'description' => 'Layanan lama — dialihkan ke Perubahan Data Mahasiswa Aktif.',
                'is_active' => false,
                'sort_order' => 99,
            ],
            [
                'code' => 'SKAK',
                'name' => 'Surat Keterangan Aktif Kuliah (Legacy)',
                'slug' => 'surat-aktif-kuliah',
                'description' => 'Layanan lama — dialihkan ke Surat Keterangan Aktif Kuliah.',
                'is_active' => false,
                'sort_order' => 99,
            ],
            [
                'code' => 'AKTIFKULIAH',
                'name' => 'Surat Aktif Kuliah (Legacy)',
                'slug' => 'aktifkuliah',
                'description' => 'Layanan lama — dialihkan ke Surat Keterangan Aktif Kuliah.',
                'is_active' => false,
                'sort_order' => 99,
            ],
            [
                'code' => 'SKAL',
                'name' => 'Surat Keterangan Alumni (Legacy)',
                'slug' => 'surat-keterangan-alumni',
                'description' => 'Layanan lama — dialihkan ke Surat Keterangan Verifikasi Ijazah Alumni.',
                'is_active' => false,
                'sort_order' => 99,
            ],
            [
                'code' => 'MASALAH_SIAKAD',
                'name' => 'Masalah SIAKAD (Legacy)',
                'slug' => 'masalahsiakad',
                'description' => 'Layanan lama — dialihkan ke Layanan Sistem Informasi Akademik (SIAKAD).',
                'is_active' => false,
                'sort_order' => 99,
            ],

            [
                'code' => 'KETREGIS_LEGACY',
                'name' => 'Ketregis (Legacy)',
                'slug' => 'ketregis',
                'description' => 'Layanan lama — dialihkan ke Surat Keterangan Registrasi.',
                'is_active' => false,
                'sort_order' => 99,
            ],
            [
                'code' => 'KETCUTI_LEGACY',
                'name' => 'Ketcuti (Legacy)',
                'slug' => 'ketcuti',
                'description' => 'Layanan lama — dialihkan ke Surat Cuti Mahasiswa.',
                'is_active' => false,
                'sort_order' => 99,
            ],
            [
                'code' => 'KETALUMNI_LEGACY',
                'name' => 'Ketalumni (Legacy)',
                'slug' => 'ketalumni',
                'description' => 'Layanan lama — dialihkan ke Surat Keterangan Verifikasi Ijazah Alumni.',
                'is_active' => false,
                'sort_order' => 99,
            ],
        ];

        foreach ($legacyServices as $service) {
            $existing = ServiceType::where('code', $service['code'])
                ->orWhere('slug', $service['slug'])
                ->first();
        
            if ($existing) {
                $existing->update([
                    'code' => $service['code'],
                    'name' => $service['name'],
                    'slug' => $service['slug'],
                    'description' => $service['description'],
                    'is_active' => $service['is_active'],
                    'sort_order' => $service['sort_order'],
                ]);
            } else {
                ServiceType::create($service);
            }
        }
    }
}
