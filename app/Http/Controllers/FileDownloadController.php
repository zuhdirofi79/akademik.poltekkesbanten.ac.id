<?php

namespace App\Http\Controllers;

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
            if ($file->uploaded_by === 'applicant') {
                $this->activityLogger->log(
                    'download_supporting_file',
                    "Download file pendukung {$file->original_name} pengajuan {$submission->ticket_number}",
                    $request->user(),
                );
            }

            return $this->fileStorage->downloadResponse($file);
        }

        if ($request->hasValidSignature()) {
            if (! $submission->canDownloadResults() || $file->file_category !== 'final_result') {
                abort(403, 'File belum tersedia untuk diunduh.');
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

        return $this->fileStorage->downloadResponse($file);
    }
}
