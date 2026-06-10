<?php

namespace App\Support;

class ServiceFormConfig
{
    public static function legacySlugMap(): array
    {
        return [
            'perbaikan-data-mahasiswa' => 'perubahan-data-mahasiswa-aktif',
            'surat-aktif-kuliah' => 'surat-keterangan-aktif-kuliah',
            'aktifkuliah' => 'surat-keterangan-aktif-kuliah',
            'masalahsiakad' => 'siakad',
            'surat-keterangan-alumni' => 'surat-keterangan-verifikasi-ijazah-alumni',
            'ketregis' => 'surat-keterangan-registrasi',
            'ketcuti' => 'surat-cuti-mahasiswa',
            'ketalumni' => 'surat-keterangan-verifikasi-ijazah-alumni',
        ];
    }

    public static function resolveSlug(string $slug): string
    {
        return self::legacySlugMap()[$slug] ?? $slug;
    }

    public static function isLegacySlug(string $slug): bool
    {
        return array_key_exists($slug, self::legacySlugMap());
    }

    public static function activeSlugs(): array
    {
        return array_keys(self::all());
    }

    public static function semesterSlugs(): array
    {
        return [
            'surat-keterangan-aktif-kuliah',
            'surat-keterangan-registrasi',
            'surat-cuti-mahasiswa',
            'surat-keterangan-beasiswa',
            'surat-keterangan-berkelakuan-baik',
            'surat-rekomendasi',
        ];
    }

    public static function aktifKuliahPatternSlugs(): array
    {
        return [
            'surat-keterangan-aktif-kuliah',
            'surat-keterangan-beasiswa',
            'surat-keterangan-berkelakuan-baik',
            'surat-rekomendasi',
        ];
    }

    public static function all(): array
    {
        return [
            'perubahan-data-mahasiswa-aktif' => [
                'slug' => 'perubahan-data-mahasiswa-aktif',
                'title' => 'Usulan Perubahan Data Mahasiswa Aktif',
                'card_title' => 'Usulan Perubahan Data Mahasiswa Aktif',
                'submit_label' => 'Ajukan Usulan Perubahan Data Mahasiswa Aktif',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan perubahan data mahasiswa aktif. Siapkan data dukung kesesuaian data seperti KTP, ijazah sebelumnya, kartu keluarga, atau dokumen lain yang relevan.',
                'request_types' => [
                    'Perbaikan NIM',
                    'Perbaikan Nama',
                    'Perbaikan Tempat Lahir',
                    'Perbaikan Tanggal Lahir',
                    'Perbaikan NIK',
                    'Perbaikan Nama Ibu Kandung',
                    'Lainnya',
                ],
                'file_fields' => [
                    'supporting_data' => 'Data Dukung Kesesuaian Data (PDF/JPG/PNG)',
                ],
            ],
            'surat-keterangan-aktif-kuliah' => [
                'slug' => 'surat-keterangan-aktif-kuliah',
                'title' => 'Usulan Surat Keterangan Aktif Kuliah',
                'card_title' => 'Usulan Surat Keterangan Aktif Kuliah',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Aktif Kuliah',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Aktif Kuliah. Siapkan bukti registrasi dan KRS periode aktif.',
                'purpose_label' => 'Keperluan Surat',
                'file_fields' => [
                    'registration_proof' => 'Data Dukung - Bukti Registrasi',
                    'active_krs' => 'Data Dukung - KRS Periode Aktif',
                ],
            ],
            'surat-keterangan-registrasi' => [
                'slug' => 'surat-keterangan-registrasi',
                'title' => 'Usulan Surat Keterangan Registrasi',
                'card_title' => 'Usulan Surat Keterangan Registrasi',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Registrasi',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Registrasi. Siapkan bukti registrasi semester aktif.',
                'purpose_label' => 'Keperluan Surat',
                'file_fields' => [
                    'student_registration_proof' => 'Data Dukung - Bukti Registrasi Mahasiswa',
                ],
            ],
            'surat-cuti-mahasiswa' => [
                'slug' => 'surat-cuti-mahasiswa',
                'title' => 'Usulan Surat Cuti Mahasiswa',
                'card_title' => 'Usulan Surat Cuti Mahasiswa',
                'submit_label' => 'Ajukan Usulan Surat Cuti Mahasiswa',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Cuti Mahasiswa. Siapkan bukti registrasi cuti dan surat permohonan cuti.',
                'file_fields' => [
                    'leave_registration_proof' => 'Data Dukung - Bukti Registrasi Cuti',
                    'leave_request_letter' => 'Data Dukung - Surat Permohonan Cuti',
                ],
            ],
            'surat-keterangan-beasiswa' => [
                'slug' => 'surat-keterangan-beasiswa',
                'title' => 'Usulan Surat Keterangan Beasiswa',
                'card_title' => 'Usulan Surat Keterangan Beasiswa',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Beasiswa',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Beasiswa. Siapkan data akademik dan dokumen pendukung yang diperlukan.',
                'purpose_label' => 'Keperluan Surat',
                'file_fields' => [
                    'registration_proof' => 'Data Dukung - Bukti Registrasi',
                    'active_krs' => 'Data Dukung - KRS Periode Aktif',
                ],
            ],
            'surat-keterangan-berkelakuan-baik' => [
                'slug' => 'surat-keterangan-berkelakuan-baik',
                'title' => 'Usulan Surat Keterangan Berkelakuan Baik',
                'card_title' => 'Usulan Surat Keterangan Berkelakuan Baik',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Berkelakuan Baik',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Berkelakuan Baik. Pastikan data identitas dan dokumen pendukung sudah sesuai.',
                'purpose_label' => 'Keperluan Surat',
                'file_fields' => [
                    'registration_proof' => 'Data Dukung - Bukti Registrasi',
                    'active_krs' => 'Data Dukung - KRS Periode Aktif',
                ],
            ],
            'surat-keterangan-verifikasi-ijazah-alumni' => [
                'slug' => 'surat-keterangan-verifikasi-ijazah-alumni',
                'title' => 'Usulan Surat Keterangan Verifikasi Ijazah Alumni',
                'card_title' => 'Usulan Surat Keterangan Verifikasi Ijazah Alumni',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Verifikasi Ijazah Alumni',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Verifikasi Ijazah Alumni. Siapkan ijazah dan data pendukung alumni.',
                'purpose_label' => 'Keperluan Verifikasi',
                'file_fields' => [
                    'diploma' => 'Data Dukung - Ijazah',
                    'additional_supporting_data' => 'Data Dukung Tambahan (opsional)',
                ],
            ],
            'surat-rekomendasi' => [
                'slug' => 'surat-rekomendasi',
                'title' => 'Usulan Surat Rekomendasi Mahasiswa/Alumni',
                'card_title' => 'Usulan Surat Rekomendasi Mahasiswa/Alumni',
                'submit_label' => 'Ajukan Usulan Surat Rekomendasi Mahasiswa/Alumni',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Rekomendasi Mahasiswa/Alumni, termasuk untuk keperluan magang dan keperluan lainnya. Siapkan bukti registrasi dan KRS periode aktif jika masih mahasiswa aktif, atau dokumen pendukung lain jika alumni.',
                'purpose_label' => 'Keperluan Surat Rekomendasi',
                'file_fields' => [
                    'registration_proof' => 'Data Dukung - Bukti Registrasi',
                    'active_krs' => 'Data Dukung - KRS Periode Aktif',
                ],
            ],
            'siakad' => [
                'slug' => 'siakad',
                'title' => 'Kendala Sistem Informasi Akademik (SIAKAD)',
                'card_title' => 'Kendala Sistem Informasi Akademik (SIAKAD)',
                'submit_label' => 'Ajukan Laporan Kendala SIAKAD',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk melaporkan kendala pada Sistem Informasi Akademik (SIAKAD). Isi data dengan benar agar admin dapat melakukan pengecekan.',
                'issue_types' => [
                    'Lupa Password',
                    'Tidak Bisa Login',
                    'Pengambilan KRS',
                    'Cetak KRS Periode Lampau',
                    'Cetak Kartu Rencana Studi',
                    'Cetak Kartu Hasil Studi',
                    'Kendala Lainnya',
                ],
                'additional_note_placeholder' => 'Keterangan lainnya hanya untuk cetak KRS periode lampau, misalnya cetak KRS periode 2025/2026 Genap. Untuk kendala lainnya silakan isi (-).',
                'file_fields' => [
                    'optional_supporting_data' => 'Data Dukung (opsional)',
                ],
            ],
        ];
    }

    public static function get(string $slug): ?array
    {
        $resolved = self::resolveSlug($slug);

        return self::all()[$resolved] ?? null;
    }
}
