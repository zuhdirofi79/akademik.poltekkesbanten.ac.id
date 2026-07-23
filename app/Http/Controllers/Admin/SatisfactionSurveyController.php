<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SurveyStatsService;
use Illuminate\Http\Request;

class SatisfactionSurveyController extends Controller
{
    public function __construct(private SurveyStatsService $stats) {}

    public function index(Request $request)
    {
        $summary = $this->stats->summary($request);

        return view('admin.surveys.index', [
            'surveys' => $this->stats->filteredQuery($request)
                ->with('serviceType')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'serviceTypes' => $this->stats->serviceTypesForFilter(),
            'stats' => $summary,
            'periodLabel' => $this->stats->periodLabel($request),
            'filters' => $request->only(['date_from', 'date_to', 'service_type_id', 'satisfaction_level']),
        ]);
    }
}
