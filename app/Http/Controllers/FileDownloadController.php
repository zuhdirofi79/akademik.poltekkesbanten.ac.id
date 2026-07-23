<?php

namespace App\Http\Controllers;

use App\Enums\FileCategory;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Services\AdminActivityLogger;
use App\Services\FileStorageService;
use Illuminate\Http\Request;

class FileDownloadController extends Controller
{
    public function __construct(
        private FileStorageService $fileStorage,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function download(Request $request, Submission $submission, SubmissionFile $file)
    {
        abort_if($file->submission_id !== $submission->id, 404);

        if ($request->user()?->isStaff()) {
            abort_unless($request->user()->canAccessSubmission($submission), 403);

            if ($file->uploaded_by === 'applicant') {
                $this->activityLogger->log(
                    'download_supporting_file',
                    "Download file pendukung {$file->original_name} pengajuan {$submission->ticket_number}",
                    $request->user(),
                );
            }

            return $this->fileStorage->downloadResponse($file);
        }

        $isFinalResult = $file->file_category === FileCategory::FinalResult->value
            || $file->uploaded_by === 'admin';

        if ($isFinalResult && $submission->requiresSatisfactionSurveyBeforeDownload()) {
            return redirect()
                ->route('survey.create', ['ticket' => $submission->ticket_number])
                ->with(
                    'warning',
                    'Silakan isi Survei Kepuasan Layanan Akademik terlebih dahulu sebelum mengunduh file hasil layanan.'
                );
        }

        if ($request->hasValidSignature()) {
            if (! $submission->canDownloadResults() || $file->file_category !== FileCategory::FinalResult->value) {
                abort(403, 'File belum tersedia untuk diunduh.');
            }

            if (! $submission->hasSatisfactionSurvey()) {
                return redirect()
                    ->route('survey.create', ['ticket' => $submission->ticket_number])
                    ->with(
                        'warning',
                        'Silakan isi Survei Kepuasan Layanan Akademik terlebih dahulu sebelum mengunduh file hasil layanan.'
                    );
            }

            return $this->fileStorage->downloadResponse($file);
        }

        $token = $request->query('token');
        if (! $token || ! hash_equals($submission->progress_token, $token)) {
            abort(403, 'Token tidak valid.');
        }

        if ($file->uploaded_by === 'admin' && ! $submission->canDownloadResults()) {
            abort(403, 'File hasil belum tersedia.');
        }

        if ($isFinalResult && ! $submission->hasSatisfactionSurvey()) {
            return redirect()
                ->route('survey.create', ['ticket' => $submission->ticket_number])
                ->with(
                    'warning',
                    'Silakan isi Survei Kepuasan Layanan Akademik terlebih dahulu sebelum mengunduh file hasil layanan.'
                );
        }

        return $this->fileStorage->downloadResponse($file);
    }
}
