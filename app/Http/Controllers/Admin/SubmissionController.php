<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSubmissionStatusRequest;
use App\Http\Requests\Admin\UploadFinalFileRequest;
use App\Mail\SubmissionCompletedMail;
use App\Models\ServiceType;
use App\Models\StudyProgram;
use App\Models\Submission;
use App\Services\AdminActivityLogger;
use App\Services\FileStorageService;
use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function __construct(
        private SubmissionService $submissionService,
        private FileStorageService $fileStorage,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $studyPrograms = $user->isAdminJurusan() && $user->study_program_id
            ? StudyProgram::where('id', $user->study_program_id)->get()
            : StudyProgram::orderBy('name')->get();

        $query = $this->buildFilteredQuery($request)
            ->with(['serviceType', 'studyProgram']);

        return view('admin.submissions.index', [
            'submissions' => $query->paginate(20)->withQueryString(),
            'serviceTypes' => ServiceType::orderByRaw('is_active DESC')->orderBy('sort_order')->orderBy('name')->get(),
            'studyPrograms' => $studyPrograms,
            'statuses' => SubmissionStatus::adminOptions(),
        ]);
    }

    public function show(Submission $submission)
    {
        abort_unless(auth()->user()->canAccessSubmission($submission), 403);
        $submission->load([
            'serviceType',
            'studyProgram',
            'files',
            'statusHistories.changedByUser',
            'jurusanVerifiedBy',
            'centralVerifiedBy',
            'satisfactionSurvey',
        ]);

        $user = auth()->user();

        return view('admin.submissions.show', [
            'submission' => $submission,
            'statuses' => SubmissionStatus::adminOptions(),
            'progressPageUrl' => route('progress.index'),
            'canVerifyJurusan' => $user->canVerifyJurusan($submission),
            'canVerifyCentral' => $user->canVerifyCentral($submission),
        ]);
    }

    public function verifyJurusan(Request $request, Submission $submission)
    {
        abort_unless($request->user()->canAccessSubmission($submission), 403);
        abort_unless($submission->requiresSkVerification(), 404);

        if (! $request->user()->canVerifyJurusan($submission)) {
            return back()->with('error', 'Anda tidak berwenang melakukan verifikasi ADAK Jurusan untuk pengajuan ini.');
        }

        $note = $request->validate([
            'jurusan_verification_note' => ['nullable', 'string', 'max:5000'],
        ])['jurusan_verification_note'] ?? null;

        $oldStatus = $submission->status;

        $submission->update([
            'jurusan_verified_at' => now(),
            'jurusan_verified_by' => $request->user()->id,
            'jurusan_verification_note' => $note,
            'status' => SubmissionStatus::JurusanVerified->value,
        ]);

        $submission->statusHistories()->create([
            'old_status' => $oldStatus,
            'new_status' => SubmissionStatus::JurusanVerified->value,
            'note' => $note ?: 'Diverifikasi ADAK Jurusan.',
            'changed_by' => $request->user()->id,
        ]);

        $this->activityLogger->log(
            'verify_jurusan',
            "Verifikasi ADAK Jurusan pengajuan {$submission->ticket_number}",
        );

        return back()->with('success', 'Pengajuan berhasil diverifikasi ADAK Jurusan.');
    }

    public function verifyCentral(Request $request, Submission $submission)
    {
        abort_unless($request->user()->canAccessSubmission($submission), 403);
        abort_unless($submission->requiresSkVerification(), 404);

        if (! $submission->jurusan_verified_at) {
            return back()->with('error', 'Pengajuan harus diverifikasi ADAK Jurusan terlebih dahulu.');
        }

        if (! $request->user()->canVerifyCentral($submission)) {
            return back()->with('error', 'Anda tidak berwenang melakukan verifikasi Admin Pusat untuk pengajuan ini.');
        }

        $note = $request->validate([
            'central_verification_note' => ['nullable', 'string', 'max:5000'],
        ])['central_verification_note'] ?? null;

        $oldStatus = $submission->status;

        $submission->update([
            'central_verified_at' => now(),
            'central_verified_by' => $request->user()->id,
            'central_verification_note' => $note,
            'status' => SubmissionStatus::CentralVerified->value,
        ]);

        $submission->statusHistories()->create([
            'old_status' => $oldStatus,
            'new_status' => SubmissionStatus::CentralVerified->value,
            'note' => $note ?: 'Diverifikasi Admin Pusat / Proses SK.',
            'changed_by' => $request->user()->id,
        ]);

        $this->activityLogger->log(
            'verify_central',
            "Verifikasi Admin Pusat pengajuan {$submission->ticket_number}",
        );

        return back()->with('success', 'Pengajuan berhasil diverifikasi Admin Pusat untuk tindak lanjut SK.');
    }

    public function updateStatus(UpdateSubmissionStatusRequest $request, Submission $submission)
    {
        abort_unless($request->user()->canAccessSubmission($submission), 403);

        $oldStatus = $submission->status;

        $this->submissionService->updateStatus(
            $submission,
            $request->validated('status'),
            $request->validated('admin_note'),
            $request->user()
        );

        $this->activityLogger->log(
            'update_status',
            "Ubah status pengajuan {$submission->ticket_number} dari {$oldStatus} ke {$request->validated('status')}",
        );

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function uploadFinalFile(UploadFinalFileRequest $request, Submission $submission)
    {
        abort_unless($request->user()->canAccessSubmission($submission), 403);

        $this->fileStorage->storeAdminFile($submission, $request->file('file_final'));

        $adminNote = $request->validated('catatan_admin') ?? $submission->admin_note;
        $autoStatus = $request->boolean('ubah_status_otomatis', true);

        if ($autoStatus) {
            $this->submissionService->updateStatus(
                $submission,
                SubmissionStatus::Completed->value,
                $adminNote,
                $request->user(),
                notify: false
            );

            $submission->update([
                'ready_to_send_email' => true,
                'email_sent_at' => null,
                'email_error' => null,
            ]);
        } elseif ($adminNote !== $submission->admin_note) {
            $submission->update(['admin_note' => $adminNote]);
        }

        $this->activityLogger->log(
            'upload_final_file',
            "Upload file final pengajuan {$submission->ticket_number}",
        );

        return back()->with(
            'success',
            'File final berhasil diupload. Sistem akan mengirim email otomatis ke mahasiswa melalui cron job.'
        );
    }

    public function sendEmail(Request $request, Submission $submission)
    {
        abort_unless($request->user()->canAccessSubmission($submission), 403);

        if (! $submission->hasFinalFiles()) {
            return back()->with('error', 'Belum ada file hasil untuk dikirim.');
        }

        try {
            Mail::to($submission->email)->send(new SubmissionCompletedMail($submission->load('finalFiles', 'serviceType')));

            $submission->update([
                'email_sent_at' => now(),
                'email_error' => null,
                'status' => SubmissionStatus::EmailSent->value,
                'ready_to_send_email' => false,
            ]);

            $submission->statusHistories()->create([
                'old_status' => SubmissionStatus::Completed->value,
                'new_status' => SubmissionStatus::EmailSent->value,
                'note' => 'Email file hasil dikirim manual oleh admin.',
                'changed_by' => $request->user()->id,
            ]);

            $this->activityLogger->log(
                'resend_email',
                "Kirim ulang email hasil pengajuan {$submission->ticket_number} ke {$submission->email}",
            );

            return back()->with('success', 'Email berhasil dikirim.');
        } catch (\Throwable $e) {
            $submission->update(['email_error' => $e->getMessage()]);

            return back()->with('error', 'Gagal mengirim email: '.$e->getMessage());
        }
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->buildFilteredQuery($request);

        $filename = 'pengajuan-'.now()->format('Ymd-His').'.csv';

        $this->activityLogger->log('export_submissions', 'Export data pengajuan ke CSV');

        return response()->streamDownload(function () use ($query) {
            $query = $query->with(['serviceType', 'studyProgram']);
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Nomor Tiket', 'Layanan', 'Program Studi', 'NIM', 'Nama', 'Email', 'Nomor HP/Telepon',
                'Status', 'Tanggal Pengajuan',
            ]);

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->ticket_number,
                        $row->serviceType->name,
                        $row->studyProgram->name,
                        $row->nim,
                        $row->full_name,
                        $row->email,
                        $row->whatsapp,
                        $row->status,
                        $row->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = Submission::query()->latest();

        $user = $request->user();
        if ($user && $user->isAdminJurusan() && $user->study_program_id) {
            $query->where('study_program_id', $user->study_program_id);
        }

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('verification_filter')) {
            match ($request->verification_filter) {
                'belum_jurusan' => $query->whereNull('jurusan_verified_at')
                    ->whereHas('serviceType', fn ($q) => $q->whereIn('slug', ['surat-cuti-mahasiswa', 'surat-keluar-mahasiswa'])),
                'sudah_jurusan' => $query->whereNotNull('jurusan_verified_at')->whereNull('central_verified_at'),
                'sudah_pusat' => $query->whereNotNull('central_verified_at'),
                default => null,
            };
        }

        if ($request->filled('study_program_id')) {
            $query->where('study_program_id', $request->study_program_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
