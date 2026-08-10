<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AcademicQueueService;
use App\Services\ProgressStatsService;

class DashboardController extends Controller
{
    public function __construct(
        private ProgressStatsService $progressStats,
        private AcademicQueueService $queueService,
    ) {}

    public function index()
    {
        $user = auth()->user();
        $studyProgramId = $user->isAdminJurusan() ? $user->study_program_id : null;

        return view('admin.dashboard', [
            'stats' => $this->progressStats->dashboardStats($studyProgramId),
            'queueStats' => $this->queueService->dashboardStats(),
        ]);
    }
}
