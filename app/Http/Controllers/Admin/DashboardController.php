<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProgressStatsService;

class DashboardController extends Controller
{
    public function __construct(private ProgressStatsService $progressStats) {}

    public function index()
    {
        $user = auth()->user();
        $studyProgramId = $user->isAdminJurusan() ? $user->study_program_id : null;

        return view('admin.dashboard', [
            'stats' => $this->progressStats->dashboardStats($studyProgramId),
        ]);
    }
}
