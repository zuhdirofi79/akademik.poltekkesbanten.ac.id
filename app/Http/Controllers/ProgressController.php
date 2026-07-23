<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgressCheckRequest;
use App\Models\Submission;
use App\Services\ProgressStatsService;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(private ProgressStatsService $progressStats) {}

    public function index()
    {
        return view('public.progress', [
            'submission' => null,
            'serviceStats' => $this->progressStats->progressPageStats(),
        ]);
    }

    public function check(ProgressCheckRequest $request)
    {
        $identifier = trim($request->input('identifier'));
        $token = trim($request->input('progress_token'));

        $submission = $this->findSubmission($identifier, $token);

        if (! $submission) {
            return back()
                ->withInput()
                ->with('error', 'Data pengajuan tidak ditemukan. Periksa NIM/email dan nomor tiket/token.');
        }

        return $this->renderProgress($submission);
    }

    public function result(Request $request)
    {
        $ticket = trim((string) $request->query('ticket'));
        $token = trim((string) $request->query('token'));

        if ($ticket === '' || $token === '') {
            return redirect()
                ->route('progress.index')
                ->with('error', 'Data progres tidak lengkap. Silakan cek ulang dengan NIM/email dan nomor tiket.');
        }

        $submission = Submission::with(['serviceType', 'studyProgram', 'finalFiles', 'satisfactionSurvey'])
            ->where('ticket_number', $ticket)
            ->where('progress_token', $token)
            ->first();

        if (! $submission) {
            return redirect()
                ->route('progress.index')
                ->with('error', 'Data pengajuan tidak ditemukan. Silakan cek ulang progres layanan.');
        }

        return $this->renderProgress($submission);
    }

    private function findSubmission(string $identifier, string $token): ?Submission
    {
        return Submission::with(['serviceType', 'studyProgram', 'finalFiles', 'satisfactionSurvey'])
            ->where(function ($query) use ($identifier) {
                $query->where('nim', $identifier)
                    ->orWhere('email', $identifier);
            })
            ->where(function ($query) use ($token) {
                $query->where('progress_token', $token)
                    ->orWhere('ticket_number', $token);
            })
            ->first();
    }

    private function renderProgress(Submission $submission)
    {
        return view('public.progress', [
            'submission' => $submission,
            'serviceStats' => $this->progressStats->progressPageStats(),
            'hasSurvey' => $submission->hasSatisfactionSurvey(),
            'requiresSurvey' => $submission->requiresSatisfactionSurveyBeforeDownload(),
            'canPublicDownload' => $submission->canPublicDownloadFinal(),
        ]);
    }
}
