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
use App\Services\WhatsAppNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function __construct(
        private SubmissionService $submissionService,
        private FileStorageService $fileStorage,
        private WhatsAppNormalizer $whatsApp,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request)
            ->with(['serviceType', 'studyProgram']);

        return view('admin.submissions.index', [
            'submissions' => $query->paginate(20)->withQueryString(),
            'serviceTypes' => ServiceType::orderByRaw('is_active DESC')->orderBy('sort_order')->orderBy('name')->get(),
            'studyPrograms' => StudyProgram::orderBy('name')->get(),
            'statuses' => SubmissionStatus::adminOptions(),
        ]);
    }

    public function show(Submission $submission)
    {
        $submission->load(['serviceType', 'studyProgram', 'files', 'statusHistories.changedByUser']);

        $firstFinalFile = $submission->finalFiles()->first();
        $signedDownloadUrl = $firstFinalFile
            ? URL::temporarySignedRoute(
                'download.signed',
                now()->addDays(7),
                ['submission' => $submission->id, 'file' => $firstFinalFile->id]
            )
            : null;

        $whatsappMessage = "Halo {$submission->full_name},\n"
            ."Pengajuan {$submission->serviceType->name} dengan nomor tiket {$submission->ticket_number}.\n"
            .($signedDownloadUrl
                ? "Silakan unduh file melalui link berikut:\n{$signedDownloadUrl}\n\n"
                : "Silakan cek progres pengajuan Anda di halaman Progres Layanan.\n\n")
            ."Terima kasih.\n"
            .'Layanan Akademik Poltekkes Kemenkes Banten';

        return view('admin.submissions.show', [
            'submission' => $submission,
            'statuses' => SubmissionStatus::adminOptions(),
            'whatsappUrl' => $this->whatsApp->waLink($submission->whatsapp, $whatsappMessage),
            'progressPageUrl' => route('progress.index'),
        ]);
    }

    public function updateStatus(UpdateSubmissionStatusRequest $request, Submission $submission)
    {
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
                'Nomor Tiket', 'Layanan', 'Program Studi', 'NIM', 'Nama', 'Email', 'WhatsApp',
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

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
