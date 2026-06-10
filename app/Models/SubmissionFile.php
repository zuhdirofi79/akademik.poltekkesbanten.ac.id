<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    protected $fillable = [
        'submission_id',
        'file_category',
        'original_name',
        'stored_path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
