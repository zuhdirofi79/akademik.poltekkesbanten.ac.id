<?php

namespace App\Mail;

use App\Models\Submission;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SubmissionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('submission_completed', $this->replacements(), [
            'subject' => 'File Layanan Akademik Tersedia - '.$this->submission->ticket_number,
            'body' => null,
        ]);

        return new Envelope(subject: $rendered['subject']);
    }

    public function content(): Content
    {
        $downloadLinks = $this->submission->finalFiles->map(function ($file) {
            return [
                'name' => $file->original_name,
                'url' => URL::temporarySignedRoute(
                    'download.signed',
                    now()->addDays(7),
                    ['submission' => $this->submission->id, 'file' => $file->id]
                ),
            ];
        });

        return new Content(
            markdown: 'emails.submission-completed',
            with: [
                'submission' => $this->submission->load('serviceType'),
                'progressUrl' => route('progress.index'),
                'surveyUrl' => route('survey.create', ['ticket' => $this->submission->ticket_number]),
                'downloadLinks' => $downloadLinks,
            ],
        );
    }

    private function replacements(): array
    {
        return [
            'nama' => $this->submission->full_name,
            'layanan' => $this->submission->serviceType->name,
            'tiket' => $this->submission->ticket_number,
            'link_progres' => route('progress.index'),
        ];
    }
}
