<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Submitted = 'Diajukan';
    case Processing = 'Diproses';
    case JurusanVerified = 'Diverifikasi ADAK Jurusan';
    case CentralVerified = 'Diverifikasi Admin Pusat / Proses SK';
    case NeedsRevision = 'Butuh Revisi';
    case Rejected = 'Ditolak';
    case Approved = 'Disetujui';
    case Completed = 'Selesai / File Tersedia';
    case EmailSent = 'Terkirim Email';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function adminOptions(): array
    {
        return self::values();
    }
}
