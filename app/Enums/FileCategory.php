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
    case KhsActiveProof = 'khs_active_proof';
    case StatementLetter = 'statement_letter';
    case Diploma = 'diploma';
    case AdditionalSupportingData = 'additional_supporting_data';
    case AlumniSupportingData = 'alumni_supporting_data';
    case OptionalSupportingData = 'optional_supporting_data';
    case ProposalOrInvitation = 'proposal_or_invitation';
    case InvitationOrProposal = 'invitation_or_proposal';
    case ReactivationRegistrationProof = 'reactivation_registration_proof';
    case ReactivationStatementLetter = 'reactivation_statement_letter';
    case FinalResult = 'final_result';

    case SupportingGeneral = 'supporting_general';
    case SupportingRegistration = 'supporting_registration';
    case SupportingKrs = 'supporting_krs';
    case SupportingLeaveRegistration = 'supporting_leave_registration';
    case SupportingLeaveRequest = 'supporting_leave_request';
    case SupportingKhsActiveProof = 'supporting_khs_active_proof';
    case SupportingStatementLetter = 'supporting_statement_letter';
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
            self::KhsActiveProof, self::SupportingKhsActiveProof => 'Bukti KHS Aktif',
            self::StatementLetter, self::SupportingStatementLetter => 'Surat Pernyataan Tertulis Bermaterai',
            self::Diploma, self::SupportingDiploma => 'Ijazah',
            self::AdditionalSupportingData => 'Data Dukung Tambahan',
            self::AlumniSupportingData => 'Surat Pernyataan Magang dan Scan Ijazah',
            self::OptionalSupportingData => 'Data Dukung (Opsional)',
            self::ProposalOrInvitation => 'Proposal atau Surat Undangan',
            self::InvitationOrProposal => 'Bukti Undangan / Proposal',
            self::ReactivationRegistrationProof => 'Bukti Registrasi Cuti ke Akademik Jurusan',
            self::ReactivationStatementLetter => 'Surat Pernyataan Bermaterai Aktif Kembali',
            self::FinalResult => 'File Hasil',
        };
    }

    public static function labelFor(string $category): string
    {
        $enum = self::tryFrom($category);

        return $enum?->label() ?? $category;
    }
}
