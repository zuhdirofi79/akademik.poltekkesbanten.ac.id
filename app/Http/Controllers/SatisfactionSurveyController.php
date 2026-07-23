<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSatisfactionSurveyRequest;
use App\Models\AcademicSatisfactionSurvey;
use App\Models\ServiceType;
use App\Models\Submission;
use Illuminate\Http\Request;

class SatisfactionSurveyController extends Controller
{
    public function create(Request $request)
    {
        $ticket = trim((string) ($request->query('ticket') ?: $request->query('ticket_number')));
        $submission = null;

        if ($ticket !== '') {
            $submission = Submission::with('serviceType')
                ->where('ticket_number', $ticket)
                ->first();
        }

        if ($request->filled('submission_id') && ! $submission) {
            $submission = Submission::with('serviceType')->find($request->query('submission_id'));
        }

        return view('public.survey.create', [
            'serviceTypes' => ServiceType::active()->orderBy('sort_order')->orderBy('id')->get(),
            'reasonsByLevel' => AcademicSatisfactionSurvey::reasonsByLevel(),
            'linkedSubmission' => $submission,
            'prefill' => [
                'submission_id' => $submission?->id,
                'ticket_number' => $submission?->ticket_number ?? $ticket,
                'service_type_id' => $submission?->service_type_id ?? $request->query('service_type_id'),
                'respondent_name' => $submission?->full_name,
                'respondent_email' => $submission?->email,
            ],
        ]);
    }

    public function store(StoreSatisfactionSurveyRequest $request)
    {
        $data = $request->validated();
        $submission = null;

        if (! empty($data['submission_id'])) {
            $submission = Submission::with('serviceType')->find($data['submission_id']);
        }

        if (! $submission && ! empty($data['ticket_number'])) {
            $submission = Submission::with('serviceType')
                ->where('ticket_number', $data['ticket_number'])
                ->first();
        }

        $serviceType = null;
        if (! empty($data['service_type_id'])) {
            $serviceType = ServiceType::find($data['service_type_id']);
        } elseif ($submission) {
            $serviceType = $submission->serviceType;
        }

        AcademicSatisfactionSurvey::create([
            'submission_id' => $submission?->id,
            'satisfaction_level' => $data['satisfaction_level'],
            'service_type_id' => $serviceType?->id,
            'service_name' => $serviceType?->name,
            'reason' => $data['reason'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'respondent_name' => $data['respondent_name'] ?? $submission?->full_name,
            'respondent_email' => $data['respondent_email'] ?? $submission?->email,
            'ticket_number' => $data['ticket_number'] ?? $submission?->ticket_number,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        if ($submission) {
            return redirect()
                ->route('progress.result', [
                    'ticket' => $submission->ticket_number,
                    'token' => $submission->progress_token,
                ])
                ->with('success', 'Terima kasih. Survei kepuasan telah diisi. Silakan unduh file hasil layanan Anda.');
        }

        return redirect()
            ->route('survey.create')
            ->with('success', 'Terima kasih. Survei kepuasan layanan akademik Anda telah tersimpan dan akan digunakan untuk peningkatan mutu layanan.');
    }
}
