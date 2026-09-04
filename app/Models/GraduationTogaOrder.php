<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GraduationTogaOrder extends Model
{
    protected $fillable = [
        'order_code',
        'student_name',
        'nim',
        'nik',
        'study_program_id',
        'study_program_name',
        'graduation_year',
        'email',
        'phone',
        'gender',
        'toga_size',
        'size_note',
        'quantity',
        'status',
        'payment_status',
        'payment_amount',
        'payment_method',
        'payment_proof_path',
        'payment_proof_original_name',
        'payment_proof_mime',
        'payment_proof_size',
        'payment_uploaded_at',
        'payment_verified_at',
        'payment_verified_by',
        'payment_rejected_at',
        'payment_rejected_by',
        'admin_note',
        'rejection_note',
        'public_token',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'graduation_year' => 'integer',
            'payment_amount' => 'decimal:2',
            'payment_proof_size' => 'integer',
            'payment_uploaded_at' => 'datetime',
            'payment_verified_at' => 'datetime',
            'payment_rejected_at' => 'datetime',
        ];
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function paymentVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function paymentRejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_rejected_by');
    }

    public static function generateOrderCode(): string
    {
        do {
            $code = 'WSD-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (self::where('order_code', $code)->exists());

        return $code;
    }

    public static function generatePublicToken(): string
    {
        do {
            $token = Str::random(40);
        } while (self::where('public_token', $token)->exists());

        return $token;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'payment_uploaded' => 'Bukti Diunggah',
            'payment_verified' => 'Pembayaran Terverifikasi',
            'payment_rejected' => 'Pembayaran Ditolak',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'Menunggu informasi pembayaran',
            'uploaded' => 'Menunggu verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => ucfirst((string) $this->payment_status),
        };
    }

    public function genderLabel(): string
    {
        return match ($this->gender) {
            'Laki-laki' => 'Laki-laki',
            'Perempuan' => 'Perempuan',
            default => (string) $this->gender,
        };
    }

    public function hasPaymentProof(): bool
    {
        return filled($this->payment_proof_path);
    }

    public function paymentProofIsImage(): bool
    {
        $mime = (string) $this->payment_proof_mime;

        return str_starts_with($mime, 'image/');
    }

    public function programDisplayName(): string
    {
        return $this->study_program_name
            ?: ($this->studyProgram?->name ?? '-');
    }
}
