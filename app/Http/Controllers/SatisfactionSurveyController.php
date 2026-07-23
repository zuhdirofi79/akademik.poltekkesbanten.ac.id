<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSatisfactionSurveyRequest;
use App\Models\AcademicSatisfactionSurvey;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class SatisfactionSurveyController extends Controller
{
    public function create(Request $request)
    {
        return view('public.survey.create', [
            'serviceTypes' => ServiceType::active()->orderBy('sort_order')->orderBy('id')->get(),
            'reasonsByLevel' => AcademicSatisfactionSurvey::reasonsByLevel(),
            'prefill' => [
                'ticket_number' => $request->query('ticket'),
                'service_type_id' => $request->query('service_type_id'),
            ],
        ]);
    }

    public function store(StoreSatisfactionSurveyRequest $request)
    {
        $data = $request->validated();
        $serviceType = null;

        if (! empty($data['service_type_id'])) {
            $serviceType = ServiceType::find($data['service_type_id']);
        }

        AcademicSatisfactionSurvey::create([
            'satisfaction_level' => $data['satisfaction_level'],
            'service_type_id' => $serviceType?->id,
            'service_name' => $serviceType?->name,
            'reason' => $data['reason'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'respondent_name' => $data['respondent_name'] ?? null,
            'respondent_email' => $data['respondent_email'] ?? null,
            'ticket_number' => $data['ticket_number'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return redirect()
            ->route('survey.create')
            ->with('success', 'Terima kasih. Jawaban survei kepuasan Anda telah berhasil dikirim.');
    }
}
