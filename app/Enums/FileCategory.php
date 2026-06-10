<?php

namespace App\Enums;

enum FileCategory: string
{
    case SupportingData = 'supporting_data';
    case RegistrationProof = 'registration_proof';
    case ActiveKrs = 'active_krs';
    case StudentRegistrationProof = 'student_registration_proof';
    case LeaveRegistrationProof = 'leave_registration_proof';
    case LeaveRequestLetter = 'leave_request_letter';
    case Diploma = 'diploma';
    case AdditionalSupportingData = 'additional_supporting_data';
    case OptionalSupportingData = 'optional_supporting_data';
    case FinalResult = 'final_result';

    case SupportingGeneral = 'supporting_general';
    case SupportingRegistration = 'supporting_registration';
    case SupportingKrs = 'supporting_krs';
    case SupportingLeaveRegistration = 'supporting_leave_registration';
    case SupportingLeaveRequest = 'supporting_leave_request';
    case SupportingDiploma = 'supporting_diploma';

    public function label(): string
    {
        return match ($this) {
            self::SupportingData, self::SupportingGeneral => 'Data Dukung Kesesuaian Data',
            self::RegistrationProof, self::SupportingRegistration => 'Bukti Registrasi',
            self::ActiveKrs, self::SupportingKrs => 'KRS Periode Aktif',
            self::StudentRegistrationProof => 'Bukti Registrasi Mahasiswa',
            self::LeaveRegistrationProof, self::SupportingLeaveRegistration => 'Bukti Registrasi Cuti',
            self::LeaveRequestLetter, self::SupportingLeaveRequest => 'Surat Permohonan Cuti',
            self::Diploma, self::SupportingDiploma => 'Ijazah',
            self::AdditionalSupportingData => 'Data Dukung Tambahan',
            self::OptionalSupportingData => 'Data Dukung (Opsional)',
            self::FinalResult => 'File Hasil',
        };
    }

    public static function labelFor(string $category): string
    {
        $enum = self::tryFrom($category);

        return $enum?->label() ?? $category;
    }
}
