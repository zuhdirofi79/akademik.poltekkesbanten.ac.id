<?php

namespace App\Models;

use App\Enums\FileCategory;
use App\Enums\SubmissionStatus;
use App\Enums\UploadedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'ticket_number',
        'progress_token',
        'service_type_id',
        'study_program_id',
        'nim',
        'full_name',
        'nik',
        'email',
        'whatsapp',
        'semester',
        'mother_name',
        'request_type',
        'corrected_data',
        'purpose',
        'issue_type',
        'leave_reason',
        'entry_year',
        'graduation_year',
        'alumni_letter_type',
        'certificate_number',
        'additional_note',
        'status',
        'admin_note',
        'ready_to_send_email',
        'email_sent_at',
        'email_error',
        'data_confirmed',
    ];

    protected function casts(): array
    {
        return [
            'ready_to_send_email' => 'boolean',
            'email_sent_at' => 'datetime',
            'data_confirmed' => 'boolean',
        ];
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    public function applicantFiles(): HasMany
    {
        return $this->files()->where('uploaded_by', UploadedBy::Applicant->value);
    }

    public function finalFiles(): HasMany
    {
        return $this->files()->where('file_category', FileCategory::FinalResult->value);
    }

    public function hasFinalFiles(): bool
    {
        return $this->finalFiles()->exists();
    }

    public function isCompleted(): bool
    {
        return $this->status === SubmissionStatus::Completed->value;
    }

    public function canDownloadResults(): bool
    {
        return in_array($this->status, [
            SubmissionStatus::Completed->value,
            SubmissionStatus::EmailSent->value,
        ], true) && $this->hasFinalFiles();
    }
}
