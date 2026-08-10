<?php

namespace App\Http\Controllers;

use App\Models\AcademicServiceQueue;
use App\Services\AcademicQueueService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PublicQueueController extends Controller
{
    public function __construct(private AcademicQueueService $queues) {}

    public function index()
    {
        return view('public.queues.index', [
            'purposeOptions' => $this->queues->purposeOptions(),
            'visitorTypes' => AcademicServiceQueue::VISITOR_TYPES,
            'todayLabel' => $this->queues->nowJakarta()->translatedFormat('l, d F Y'),
            'stats' => $this->queues->publicStatusPayload(),
        ]);
    }

    public function kiosk()
    {
        return view('public.queues.kiosk', [
            'purposeOptions' => $this->queues->purposeOptions(),
            'visitorTypes' => AcademicServiceQueue::VISITOR_TYPES,
            'todayLabel' => $this->queues->nowJakarta()->translatedFormat('l, d F Y'),
        ]);
    }

    public function take(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => ['nullable', 'string', 'max:150'],
            'visitor_identity' => ['nullable', 'string', 'max:100'],
            'visitor_phone' => ['nullable', 'string', 'max:30'],
            'visitor_type' => ['required', Rule::in(AcademicServiceQueue::VISITOR_TYPES)],
            'purpose' => ['nullable', 'string', 'max:255'],
            'confirm' => ['accepted'],
        ], [
            'confirm.accepted' => 'Silakan konfirmasi bahwa Anda yakin ingin mengambil nomor antrean.',
        ]);

        $queue = $this->queues->takeQueue($validated);

        if ($request->boolean('from_kiosk')) {
            return redirect()
                ->route('queues.print', ['queue' => $queue, 'autoprint' => 1])
                ->with('success', 'Nomor antrean berhasil diambil.');
        }

        return redirect()
            ->route('queues.ticket', $queue)
            ->with('success', 'Nomor antrean berhasil diambil.');
    }

    public function ticket(AcademicServiceQueue $queue)
    {
        return view('public.queues.ticket', [
            'queue' => $queue,
            'waitingAhead' => $this->queues->waitingAheadCount($queue),
        ]);
    }

    public function print(AcademicServiceQueue $queue)
    {
        return view('public.queues.print', [
            'queue' => $queue,
        ]);
    }

    public function display()
    {
        return view('public.queues.display', [
            'initial' => $this->queues->publicStatusPayload(),
        ]);
    }

    public function status()
    {
        return response()->json($this->queues->publicStatusPayload());
    }
}
