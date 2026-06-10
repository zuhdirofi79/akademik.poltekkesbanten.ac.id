<?php

namespace App\Console\Commands;

use App\Enums\SubmissionStatus;
use App\Mail\SubmissionCompletedMail;
use App\Models\Submission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCompletedFilesCommand extends Command
{
    protected $signature = 'academic:send-completed-files';

    protected $description = 'Kirim email file hasil untuk pengajuan yang sudah selesai';

    public function handle(): int
    {
        $submissions = Submission::query()
            ->with(['serviceType', 'finalFiles'])
            ->where('status', SubmissionStatus::Completed->value)
            ->where('ready_to_send_email', true)
            ->whereNull('email_sent_at')
            ->whereHas('finalFiles')
            ->get();

        if ($submissions->isEmpty()) {
            $this->info('Tidak ada pengajuan yang perlu dikirim email.');

            return self::SUCCESS;
        }

        foreach ($submissions as $submission) {
            try {
                Mail::to($submission->email)->send(new SubmissionCompletedMail($submission));

                $submission->update([
                    'email_sent_at' => now(),
                    'email_error' => null,
                    'status' => SubmissionStatus::EmailSent->value,
                    'ready_to_send_email' => false,
                ]);

                $submission->statusHistories()->create([
                    'old_status' => SubmissionStatus::Completed->value,
                    'new_status' => SubmissionStatus::EmailSent->value,
                    'note' => 'Email file hasil dikirim otomatis oleh scheduler.',
                ]);

                $this->info("Email terkirim: {$submission->ticket_number}");
            } catch (\Throwable $e) {
                $submission->update(['email_error' => $e->getMessage()]);
                $this->error("Gagal {$submission->ticket_number}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
