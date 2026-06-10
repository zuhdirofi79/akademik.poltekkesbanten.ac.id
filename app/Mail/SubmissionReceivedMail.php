<?php

namespace App\Mail;

use App\Models\Submission;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('submission_received', $this->replacements(), [
            'subject' => 'Pengajuan Layanan Akademik - '.$this->submission->ticket_number,
            'body' => null,
        ]);

        return new Envelope(subject: $rendered['subject']);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission-received',
            with: [
                'submission' => $this->submission->load('serviceType'),
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
            'token' => $this->submission->progress_token,
            'link_progres' => route('progress.index'),
        ];
    }
}
