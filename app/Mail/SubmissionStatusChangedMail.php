<?php

namespace App\Mail;

use App\Models\Submission;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public string $oldStatus,
    ) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('submission_status_changed', $this->replacements(), [
            'subject' => 'Update Status Pengajuan - '.$this->submission->ticket_number,
            'body' => null,
        ]);

        return new Envelope(subject: $rendered['subject']);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission-status-changed',
            with: [
                'submission' => $this->submission->load('serviceType'),
                'oldStatus' => $this->oldStatus,
                'progressUrl' => route('progress.index'),
            ],
        );
    }

    private function replacements(): array
    {
        return [
            'nama' => $this->submission->full_name,
            'layanan' => $this->submission->serviceType->name,
            'tiket' => $this->submission->ticket_number,
            'status_lama' => $this->oldStatus,
            'status_baru' => $this->submission->status,
            'catatan' => $this->submission->admin_note ?? '-',
            'link_progres' => route('progress.index'),
        ];
    }
}
