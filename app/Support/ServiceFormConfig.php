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
            'surat-cuti-mahasiswa',
            'surat-keluar-mahasiswa',
            'surat-keterangan-beasiswa',
            'surat-keterangan-berkelakuan-baik',
            'surat-rekomendasi',
        ];
    }

    public static function activityGroupSlugs(): array
    {
        return [
            'surat-tugas-mahasiswa',
            'nota-dinas-dispen-kegiatan-mahasiswa',
        ];
    }

    public static function skVerificationSlugs(): array
    {
        return [
            'surat-cuti-mahasiswa',
            'surat-keluar-mahasiswa',
        ];
    }

    public static function reactivationSlug(): string
    {
        return 'pengaktifan-kembali-mahasiswa';
    }

    public static function rekomendasiSlug(): string
    {
        return 'surat-rekomendasi';
    }

    public static function berkelakuanBaikSlug(): string
    {
        return 'surat-keterangan-berkelakuan-baik';
    }

    public static function beasiswaSlug(): string
    {
        return 'surat-keterangan-beasiswa';
    }

    public static function all(): array
    {
        $adminEmail = config('akademik.admin_contact_email', 'adak.direktorat@poltekesbanten.ac.id');

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
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Aktif Kuliah. Siapkan KRS periode aktif.',
                'purpose_label' => 'Keperluan Surat',
                'file_fields' => [
                    'active_krs' => 'Data Dukung - KRS Periode Aktif',
                ],
                'file_help' => [
                    'active_krs' => 'KRS periode aktif diunduh terlebih dahulu dari SIAKAD, kemudian diunggah pada form ini.',
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
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Cuti Mahasiswa. Siapkan bukti registrasi cuti, surat permohonan cuti, dan surat pernyataan tertulis bermaterai.'
                    . "\n\n"
                    . 'Setelah mengisi data pengajuan, mohon dapat melakukan konfirmasi ke akademik jurusan untuk ditindaklanjuti dalam pembuatan Surat Keputusan.',
                'file_fields' => [
                    'leave_registration_proof' => 'Data Dukung - Bukti Registrasi Cuti',
                    'leave_request_letter' => 'Data Dukung - Surat Permohonan Cuti',
                    'statement_letter' => 'Data Dukung - Surat Pernyataan Tertulis Bermaterai',
                ],
            ],
            'surat-keluar-mahasiswa' => [
                'slug' => 'surat-keluar-mahasiswa',
                'title' => 'Usulan Surat Keluar Mahasiswa',
                'card_title' => 'Usulan Surat Keluar Mahasiswa',
                'submit_label' => 'Ajukan Usulan Surat Keluar Mahasiswa',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keluar Mahasiswa. Siapkan Bukti KHS Aktif dan Surat Pernyataan Tertulis Bermaterai.'
                    . "\n\n"
                    . 'Setelah mengisi data pengajuan, mohon dapat melakukan konfirmasi ke akademik jurusan untuk ditindaklanjuti dalam pembuatan Surat Keputusan.',
                'file_fields' => [
                    'khs_active_proof' => 'Data Dukung - Bukti KHS Aktif',
                    'statement_letter' => 'Data Dukung - Surat Pernyataan Tertulis Bermaterai',
                ],
            ],
            'surat-keterangan-beasiswa' => [
                'slug' => 'surat-keterangan-beasiswa',
                'title' => 'Usulan Surat Keterangan Beasiswa',
                'card_title' => 'Usulan Surat Keterangan Beasiswa',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Beasiswa',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Beasiswa. Siapkan KRS periode aktif dan informasi IPS/IPK terbaru.',
                'purpose_label' => 'Keperluan Surat Beasiswa Apa?',
                'file_fields' => [
                    'active_krs' => 'Data Dukung - KRS Periode Aktif',
                ],
            ],
            'surat-keterangan-berkelakuan-baik' => [
                'slug' => 'surat-keterangan-berkelakuan-baik',
                'title' => 'Usulan Surat Keterangan Berkelakuan Baik',
                'card_title' => 'Usulan Surat Keterangan Berkelakuan Baik',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Berkelakuan Baik',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Keterangan Berkelakuan Baik. Siapkan KRS periode aktif.',
                'purpose_label' => 'Keperluan Surat',
                'file_fields' => [
                    'active_krs' => 'Data Dukung - KRS Periode Aktif',
                ],
            ],
            'surat-keterangan-verifikasi-ijazah-alumni' => [
                'slug' => 'surat-keterangan-verifikasi-ijazah-alumni',
                'title' => 'Usulan Surat Keterangan Verifikasi Ijazah Alumni',
                'card_title' => 'Usulan Surat Keterangan Verifikasi Ijazah Alumni',
                'submit_label' => 'Ajukan Usulan Surat Keterangan Verifikasi Ijazah Alumni',
                'info_title' => 'Informasi Penting',
                'info_text' => 'SURAT VERIFIKASI IJAZAH ALUMNI'
                    . "\n\n"
                    . 'Layanan ini digunakan untuk pengajuan Surat Keterangan Verifikasi Ijazah Alumni. Siapkan ijazah dan data pendukung alumni.'
                    . "\n\n"
                    . 'Untuk alumni, pengajuan surat verifikasi ijazah dari perusahaan atau rumah sakit dapat dikirimkan melalui email admin akademik: '
                    . $adminEmail
                    . '.',
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
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Rekomendasi Mahasiswa/Alumni, termasuk untuk keperluan magang dan keperluan lainnya. Siapkan KRS periode aktif serta data dukung alumni berupa surat pernyataan magang dan scan ijazah.',
                'purpose_label' => 'Keperluan Surat Rekomendasi',
                'file_fields' => [
                    'alumni_supporting_data' => 'DATA DUKUNG ALUMNI - SURAT PERNYATAAN MAGANG DAN SCAN IJAZAH',
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
            'surat-tugas-mahasiswa' => [
                'slug' => 'surat-tugas-mahasiswa',
                'title' => 'Usulan Pengajuan Surat Tugas Mahasiswa',
                'card_title' => 'Usulan Pengajuan Surat Tugas Mahasiswa',
                'submit_label' => 'Ajukan Surat Tugas Mahasiswa',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Surat Tugas Mahasiswa bagi mahasiswa yang mengikuti kegiatan. Pengajuan dikoordinasikan oleh satu orang perwakilan. Siapkan proposal atau surat undangan kegiatan sebagai data dukung.',
                'file_fields' => [
                    'proposal_or_invitation' => 'Data Dukung - Proposal atau Surat Undangan',
                ],
            ],
            'nota-dinas-dispen-kegiatan-mahasiswa' => [
                'slug' => 'nota-dinas-dispen-kegiatan-mahasiswa',
                'title' => 'Usulan Pengajuan Nota Dinas Dispen Kegiatan Mahasiswa',
                'card_title' => 'Usulan Pengajuan Nota Dinas Dispen Kegiatan Mahasiswa',
                'submit_label' => 'Ajukan Nota Dinas Dispen Kegiatan Mahasiswa',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan Nota Dinas Dispensasi Kegiatan Mahasiswa. Pengajuan dikoordinasikan oleh satu orang perwakilan. Siapkan bukti undangan atau proposal kegiatan sebagai data dukung.',
                'file_fields' => [
                    'invitation_or_proposal' => 'Data Dukung - Bukti Undangan / Proposal',
                ],
            ],
            'pengaktifan-kembali-mahasiswa' => [
                'slug' => 'pengaktifan-kembali-mahasiswa',
                'title' => 'Usulan Pengajuan Pengaktifan Kembali Mahasiswa Cuti/Keluar',
                'card_title' => 'Usulan Pengajuan Pengaktifan Kembali Mahasiswa Cuti/Keluar',
                'submit_label' => 'Ajukan Pengaktifan Kembali Mahasiswa',
                'info_title' => 'Informasi Penting',
                'info_text' => 'Layanan ini digunakan untuk pengajuan pengaktifan kembali mahasiswa yang sebelumnya cuti atau keluar. Siapkan bukti registrasi cuti ke akademik jurusan dan surat pernyataan bermaterai bahwa mahasiswa aktif kembali.',
                'previous_status_options' => [
                    'Cuti',
                    'Keluar',
                ],
                'file_fields' => [
                    'reactivation_registration_proof' => 'Data Dukung - Bukti Registrasi Cuti ke Akademik Jurusan',
                    'reactivation_statement_letter' => 'Data Dukung - Surat Pernyataan Bermaterai Aktif Kembali',
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
