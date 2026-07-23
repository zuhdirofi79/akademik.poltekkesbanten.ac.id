<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SurveyStatsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveyDashboardController extends Controller
{
    public function __construct(private SurveyStatsService $stats) {}

    public function index(Request $request)
    {
        $payload = $this->stats->chartPayload($request);

        return view('admin.surveys.dashboard', [
            'summary' => $payload['summary'],
            'charts' => $payload,
            'byService' => $payload['by_service_rows'],
            'feedbackRows' => $this->stats->recentFeedback($request, 50),
            'serviceTypes' => $this->stats->serviceTypesForFilter(),
            'periodLabel' => $payload['period_label'],
            'filters' => $request->only(['date_from', 'date_to', 'service_type_id', 'satisfaction_level']),
        ]);
    }

    public function print(Request $request)
    {
        $payload = $this->stats->chartPayload($request);

        return view('admin.surveys.print', [
            'summary' => $payload['summary'],
            'charts' => $payload,
            'byService' => $payload['by_service_rows'],
            'feedbackRows' => $this->stats->recentFeedback($request, 200),
            'periodLabel' => $payload['period_label'],
            'filters' => $request->only(['date_from', 'date_to', 'service_type_id', 'satisfaction_level']),
            'generatedAt' => now(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->stats->filteredQuery($request)
            ->with('serviceType')
            ->latest();

        $filename = 'survei-kepuasan-layanan-akademik-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Tanggal',
                'Tingkat Kepuasan',
                'Jenis Layanan',
                'Alasan',
                'Saran/Masukan',
                'Nama Responden',
                'Email Responden',
                'Nomor Tiket',
                'IP Address',
            ]);

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->created_at->format('Y-m-d H:i'),
                        $row->satisfactionLabel(),
                        $row->serviceDisplayName(),
                        $row->reason ?? '-',
                        $row->feedback ?? '-',
                        $row->respondent_name ?? '-',
                        $row->respondent_email ?? '-',
                        $row->ticket_number ?? '-',
                        $row->ip_address ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
