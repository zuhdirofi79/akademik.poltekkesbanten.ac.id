<?php

namespace App\Services;

use App\Enums\FileCategory;
use App\Enums\UploadedBy;
use App\Models\Submission;
use App\Models\SubmissionFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    public function storeApplicantFile(
        Submission $submission,
        UploadedFile $file,
        string $category
    ): SubmissionFile {
        return $this->store($submission, $file, $category, UploadedBy::Applicant->value);
    }

    public function storeAdminFile(
        Submission $submission,
        UploadedFile $file
    ): SubmissionFile {
        return $this->store($submission, $file, FileCategory::FinalResult->value, UploadedBy::Admin->value);
    }

    private function store(
        Submission $submission,
        UploadedFile $file,
        string $category,
        string $uploadedBy
    ): SubmissionFile {
        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid()->toString().'.'.$extension;
        $path = "submissions/{$submission->id}/{$category}/{$storedName}";

        Storage::disk('academic')->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        return SubmissionFile::create([
            'submission_id' => $submission->id,
            'file_category' => $category,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize(),
            'uploaded_by' => $uploadedBy,
        ]);
    }

    public function downloadResponse(SubmissionFile $file)
    {
        if (! Storage::disk('academic')->exists($file->stored_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('academic')->download($file->stored_path, $file->original_name);
    }
}
