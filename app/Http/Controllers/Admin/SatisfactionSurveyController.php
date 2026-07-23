<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSatisfactionSurvey;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SatisfactionSurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicSatisfactionSurvey::query()
            ->with('serviceType')
            ->latest();

        if ($request->filled('satisfaction_level')) {
            $query->where('satisfaction_level', $request->satisfaction_level);
        }

        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $statsBase = AcademicSatisfactionSurvey::query();
        if ($request->filled('service_type_id')) {
            $statsBase->where('service_type_id', $request->service_type_id);
        }
        if ($request->filled('date_from')) {
            $statsBase->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $statsBase->whereDate('created_at', '<=', $request->date_to);
        }

        return view('admin.surveys.index', [
            'surveys' => $query->paginate(20)->withQueryString(),
            'serviceTypes' => ServiceType::active()->orderBy('sort_order')->orderBy('id')->get(),
            'stats' => [
                'puas' => (clone $statsBase)->where('satisfaction_level', 'puas')->count(),
                'cukup_puas' => (clone $statsBase)->where('satisfaction_level', 'cukup_puas')->count(),
                'kurang_puas' => (clone $statsBase)->where('satisfaction_level', 'kurang_puas')->count(),
                'total' => (clone $statsBase)->count(),
            ],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = AcademicSatisfactionSurvey::query()->with('serviceType')->latest();

        if ($request->filled('satisfaction_level')) {
            $query->where('satisfaction_level', $request->satisfaction_level);
        }
        if ($request->filled('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $filename = 'survei-kepuasan-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Tanggal',
                'Tingkat Kepuasan',
                'Jenis Layanan',
                'Alasan',
                'Saran/Masukan',
                'Nama Responden',
                'Email Responden',
                'Nomor Tiket',
            ]);

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->created_at->format('Y-m-d H:i'),
                        $row->satisfactionLabel(),
                        $row->serviceType->name ?? $row->service_name ?? '-',
                        $row->reason ?? '-',
                        $row->feedback ?? '-',
                        $row->respondent_name ?? '-',
                        $row->respondent_email ?? '-',
                        $row->ticket_number ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
