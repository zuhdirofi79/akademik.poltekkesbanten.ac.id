<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgressCheckRequest;
use App\Models\Submission;
use App\Services\ProgressStatsService;

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

        $submission = Submission::with(['serviceType', 'studyProgram', 'finalFiles'])
            ->where(function ($query) use ($identifier) {
                $query->where('nim', $identifier)
                    ->orWhere('email', $identifier);
            })
            ->where(function ($query) use ($token) {
                $query->where('progress_token', $token)
                    ->orWhere('ticket_number', $token);
            })
            ->first();

        if (! $submission) {
            return back()
                ->withInput()
                ->with('error', 'Data pengajuan tidak ditemukan. Periksa NIM/email dan nomor tiket/token.');
        }

        return view('public.progress', [
            'submission' => $submission,
            'serviceStats' => $this->progressStats->progressPageStats(),
        ]);
    }
}
