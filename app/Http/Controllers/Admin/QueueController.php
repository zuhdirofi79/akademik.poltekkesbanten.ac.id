<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicServiceQueue;
use App\Services\AcademicQueueService;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(
        private AcademicQueueService $queues,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index()
    {
        return view('admin.queues.index', [
            'initial' => $this->queues->adminStatusPayload(),
            'todayLabel' => $this->queues->nowJakarta()->translatedFormat('l, d F Y'),
        ]);
    }

    public function status()
    {
        return response()->json($this->queues->adminStatusPayload());
    }

    public function callNext()
    {
        $queue = $this->queues->callNext(auth()->user());

        if (! $queue) {
            return back()->with('warning', 'Tidak ada antrean menunggu saat ini.');
        }

        $this->activityLogger->log(
            'queue_call_next',
            'Memanggil antrean berikutnya '.$queue->queue_code.'.'
        );

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'queue' => $queue->toPublicArray(),
                'payload' => $this->queues->adminStatusPayload(),
            ]);
        }

        return back()->with('success', 'Antrean '.$queue->queue_code.' dipanggil.');
    }

    public function call(AcademicServiceQueue $queue)
    {
        $queue = $this->queues->call($queue, auth()->user());

        $this->activityLogger->log(
            'queue_call',
            'Memanggil antrean '.$queue->queue_code.'.'
        );

        return $this->actionResponse($queue, 'Antrean '.$queue->queue_code.' dipanggil.');
    }

    public function serve(AcademicServiceQueue $queue)
    {
        $queue = $this->queues->serve($queue, auth()->user());

        $this->activityLogger->log(
            'queue_serve',
            'Mulai melayani antrean '.$queue->queue_code.'.'
        );

        return $this->actionResponse($queue, 'Antrean '.$queue->queue_code.' mulai dilayani.');
    }

    public function finish(Request $request, AcademicServiceQueue $queue)
    {
        $note = $request->input('admin_note');
        $queue = $this->queues->finish($queue, auth()->user(), $note);

        $this->activityLogger->log(
            'queue_finish',
            'Menyelesaikan antrean '.$queue->queue_code.'.'
        );

        return $this->actionResponse($queue, 'Antrean '.$queue->queue_code.' selesai dilayani.');
    }

    public function skip(Request $request, AcademicServiceQueue $queue)
    {
        $note = $request->input('admin_note');
        $queue = $this->queues->skip($queue, auth()->user(), $note);

        $this->activityLogger->log(
            'queue_skip',
            'Melewati antrean '.$queue->queue_code.'.'
        );

        return $this->actionResponse($queue, 'Antrean '.$queue->queue_code.' dilewati.');
    }

    public function cancel(Request $request, AcademicServiceQueue $queue)
    {
        $note = $request->input('admin_note');
        $queue = $this->queues->cancel($queue, auth()->user(), $note);

        $this->activityLogger->log(
            'queue_cancel',
            'Membatalkan antrean '.$queue->queue_code.'.'
        );

        return $this->actionResponse($queue, 'Antrean '.$queue->queue_code.' dibatalkan.');
    }

    public function note(Request $request, AcademicServiceQueue $queue)
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $queue = $this->queues->updateNote($queue, $validated['admin_note'] ?? null);

        $this->activityLogger->log(
            'queue_note',
            'Memperbarui catatan antrean '.$queue->queue_code.'.'
        );

        return $this->actionResponse($queue, 'Catatan antrean diperbarui.');
    }

    private function actionResponse(AcademicServiceQueue $queue, string $message)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'queue' => $queue->toPublicArray(),
                'payload' => $this->queues->adminStatusPayload(),
            ]);
        }

        return back()->with('success', $message);
    }
}
