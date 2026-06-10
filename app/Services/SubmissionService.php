<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Mail\SubmissionReceivedMail;
use App\Mail\SubmissionStatusChangedMail;
use App\Models\ServiceType;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SubmissionService
{
    public function __construct(
        private TicketGenerator $ticketGenerator,
        private FileStorageService $fileStorage,
        private WhatsAppNormalizer $whatsAppNormalizer,
    ) {}

    public function create(ServiceType $serviceType, array $data, array $files = []): Submission
    {
        return DB::transaction(function () use ($serviceType, $data, $files) {
            $submission = Submission::create([
                'ticket_number' => $this->ticketGenerator->generate(),
                'progress_token' => $this->ticketGenerator->generateToken(),
                'service_type_id' => $serviceType->id,
                'study_program_id' => $data['study_program_id'],
                'nim' => $data['nim'],
                'full_name' => $data['full_name'],
                'nik' => $data['nik'],
                'email' => $data['email'],
                'whatsapp' => $this->whatsAppNormalizer->normalize($data['whatsapp']),
                'semester' => $data['semester'] ?? null,
                'gpa_score' => $data['gpa_score'] ?? null,
                'mother_name' => $data['mother_name'] ?? null,
                'request_type' => $data['request_type'] ?? null,
                'corrected_data' => $data['corrected_data'] ?? null,
                'purpose' => $data['purpose'] ?? null,
                'issue_type' => $data['issue_type'] ?? null,
                'leave_reason' => $data['leave_reason'] ?? null,
                'exit_reason' => $data['exit_reason'] ?? null,
                'entry_year' => $data['entry_year'] ?? null,
                'graduation_year' => $data['graduation_year'] ?? null,
                'alumni_letter_type' => $data['alumni_letter_type'] ?? null,
                'certificate_number' => $data['certificate_number'] ?? null,
                'additional_note' => filled($data['additional_note'] ?? null)
                    ? $data['additional_note']
                    : '-',
                'status' => SubmissionStatus::Submitted->value,
                'data_confirmed' => true,
            ]);

            foreach ($files as $category => $file) {
                if ($file instanceof UploadedFile) {
                    $this->fileStorage->storeApplicantFile($submission, $file, $category);
                }
            }

            $submission->statusHistories()->create([
                'old_status' => null,
                'new_status' => SubmissionStatus::Submitted->value,
                'note' => 'Pengajuan dibuat oleh pemohon.',
            ]);

            Mail::to($submission->email)->send(new SubmissionReceivedMail($submission));

            return $submission;
        });
    }

    public function updateStatus(
        Submission $submission,
        string $newStatus,
        ?string $adminNote = null,
        ?User $admin = null,
        bool $notify = true
    ): Submission {
        $oldStatus = $submission->status;

        $submission->update([
            'status' => $newStatus,
            'admin_note' => $adminNote ?? $submission->admin_note,
            'ready_to_send_email' => $newStatus === SubmissionStatus::Completed->value,
        ]);

        $submission->statusHistories()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'note' => $adminNote,
            'changed_by' => $admin?->id,
        ]);

        if ($notify && in_array($newStatus, [
            SubmissionStatus::NeedsRevision->value,
            SubmissionStatus::Rejected->value,
            SubmissionStatus::Approved->value,
        ], true)) {
            Mail::to($submission->email)->send(new SubmissionStatusChangedMail($submission, $oldStatus));
        }

        return $submission->fresh();
    }
}
