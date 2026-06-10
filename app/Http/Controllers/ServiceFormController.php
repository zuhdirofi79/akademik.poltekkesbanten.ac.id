<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmissionFormRequest;
use App\Models\ServiceType;
use App\Models\StudyProgram;
use App\Services\ProgressStatsService;
use App\Services\SubmissionService;
use App\Support\ServiceFormConfig;

class ServiceFormController extends Controller
{
    public function __construct(
        private SubmissionService $submissionService,
        private ProgressStatsService $progressStats,
    ) {}

    public function show(string $slug)
    {
        if (ServiceFormConfig::isLegacySlug($slug)) {
            return redirect('/layanan/'.ServiceFormConfig::resolveSlug($slug), 301);
        }

        $config = ServiceFormConfig::get($slug);
        abort_if(! $config, 404);

        $serviceType = ServiceType::where('slug', $slug)->firstOrFail();

        if (! $serviceType->is_active) {
            return redirect()
                ->route('beranda')
                ->with('error', "Layanan {$serviceType->name} saat ini tidak tersedia.");
        }

        $captcha = $this->generateCaptcha();

        return view('public.services.form', [
            'config' => $config,
            'serviceType' => $serviceType,
            'studyPrograms' => StudyProgram::active()->orderBy('name')->get(),
            'stats' => $this->progressStats->forService($serviceType),
            'captcha' => $captcha,
        ]);
    }

    public function store(SubmissionFormRequest $request, string $slug)
    {
        $slug = ServiceFormConfig::resolveSlug($slug);

        $serviceType = ServiceType::where('slug', $slug)->firstOrFail();

        if (! $serviceType->is_active) {
            abort(410, 'Layanan ini tidak tersedia.');
        }

        $config = ServiceFormConfig::get($slug);

        $files = [];
        foreach ($config['file_fields'] ?? [] as $field => $label) {
            if ($request->hasFile($field)) {
                $files[$field] = $request->file($field);
            }
        }

        $submission = $this->submissionService->create(
            $serviceType,
            $request->validated(),
            $files
        );

        return redirect()
            ->route('submission.success', $submission)
            ->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function success(int $submission)
    {
        $submission = \App\Models\Submission::with('serviceType')->findOrFail($submission);

        return view('public.submission-success', compact('submission'));
    }

    private function generateCaptcha(): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        return [
            'question' => "{$a} + {$b} = ?",
            'expected' => $a + $b,
        ];
    }
}
