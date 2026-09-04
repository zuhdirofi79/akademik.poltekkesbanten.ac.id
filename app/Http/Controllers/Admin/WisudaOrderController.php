<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraduationTogaOrder;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WisudaOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request)->with(['studyProgram', 'paymentVerifiedBy']);

        return view('admin.wisuda.orders.index', [
            'orders' => (clone $query)->latest()->paginate(25)->withQueryString(),
            'summary' => $this->summary($request),
            'studyPrograms' => $this->studyProgramsForFilter($request),
            'filters' => $request->all(),
            'statuses' => $this->orderStatuses(),
            'paymentStatuses' => $this->paymentStatuses(),
            'togaSizes' => ['All Size'],
        ]);
    }

    public function show(GraduationTogaOrder $order)
    {
        $this->authorizeOrderAccess($order);

        $order->load(['studyProgram', 'paymentVerifiedBy', 'paymentRejectedBy']);

        return view('admin.wisuda.orders.show', [
            'order' => $order,
            'config' => config('wisuda'),
        ]);
    }

    public function downloadPaymentProof(GraduationTogaOrder $order)
    {
        $this->authorizeOrderAccess($order);

        if (! $order->hasPaymentProof() || ! Storage::disk('local')->exists($order->payment_proof_path)) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $order->payment_proof_path,
            $order->payment_proof_original_name ?: basename($order->payment_proof_path)
        );
    }

    public function previewPaymentProof(GraduationTogaOrder $order)
    {
        $this->authorizeOrderAccess($order);

        if (! $order->hasPaymentProof() || ! Storage::disk('local')->exists($order->payment_proof_path)) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        return Storage::disk('local')->response(
            $order->payment_proof_path,
            $order->payment_proof_original_name ?: basename($order->payment_proof_path),
            [
                'Content-Type' => $order->payment_proof_mime ?: 'application/octet-stream',
            ]
        );
    }

    public function verifyPayment(Request $request, GraduationTogaOrder $order)
    {
        $this->authorizeOrderAccess($order);

        if (! $order->hasPaymentProof()) {
            return back()->with('error', 'Belum ada bukti pembayaran yang diunggah.');
        }

        $order->update([
            'payment_status' => 'verified',
            'status' => 'payment_verified',
            'payment_verified_at' => now(),
            'payment_verified_by' => $request->user()->id,
            'payment_rejected_at' => null,
            'payment_rejected_by' => null,
            'rejection_note' => null,
            'admin_note' => $request->input('admin_note', $order->admin_note),
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function rejectPayment(Request $request, GraduationTogaOrder $order)
    {
        $this->authorizeOrderAccess($order);

        $data = $request->validate([
            'rejection_note' => ['required', 'string', 'max:2000'],
        ]);

        $order->update([
            'payment_status' => 'rejected',
            'status' => 'payment_rejected',
            'payment_rejected_at' => now(),
            'payment_rejected_by' => $request->user()->id,
            'rejection_note' => $data['rejection_note'],
            'payment_verified_at' => null,
            'payment_verified_by' => null,
        ]);

        return back()->with('success', 'Pembayaran ditolak. Mahasiswa dapat mengunggah ulang bukti pembayaran.');
    }

    public function complete(GraduationTogaOrder $order)
    {
        $this->authorizeOrderAccess($order);

        $order->update(['status' => 'completed']);

        return back()->with('success', 'Pesanan ditandai selesai.');
    }

    public function cancel(Request $request, GraduationTogaOrder $order)
    {
        $this->authorizeOrderAccess($order);

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $order->update([
            'status' => 'cancelled',
            'admin_note' => $data['admin_note'] ?? $order->admin_note,
        ]);

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filteredQuery($request)->with(['studyProgram', 'paymentVerifiedBy'])->latest();
        $filename = 'rekap-pembayaran-toga-wisuda-'.now()->format('Ymd-Hi').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'No',
                'Tanggal Pesan',
                'Kode Pesanan',
                'Nama Mahasiswa',
                'NIM',
                'NIK',
                'Program Studi',
                'Tahun Lulus',
                'Email',
                'Nomor HP/Telepon',
                'Jenis Kelamin',
                'Ukuran Toga',
                'Catatan Ukuran',
                'Jumlah',
                'Status Pesanan',
                'Status Pembayaran',
                'Bukti Pembayaran Ada/Tidak',
                'Nama File Bukti',
                'Tanggal Upload Bukti',
                'Tanggal Verifikasi',
                'Verifikator',
                'Catatan Admin',
                'Catatan Penolakan',
            ]);

            $no = 1;
            $query->chunk(200, function ($rows) use ($handle, &$no) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $no++,
                        $row->created_at?->format('Y-m-d H:i'),
                        $row->order_code,
                        $row->student_name,
                        $row->nim,
                        $row->nik ?: '-',
                        $row->programDisplayName(),
                        $row->graduation_year ?: '-',
                        $row->email,
                        $row->phone,
                        $row->genderLabel(),
                        $row->toga_size,
                        $row->size_note ?: '-',
                        $row->quantity,
                        $row->statusLabel(),
                        $row->paymentStatusLabel(),
                        $row->hasPaymentProof() ? 'Ada' : 'Tidak',
                        $row->payment_proof_original_name ?: '-',
                        $row->payment_uploaded_at?->format('Y-m-d H:i') ?: '-',
                        $row->payment_verified_at?->format('Y-m-d H:i') ?: '-',
                        $row->paymentVerifiedBy?->name ?: '-',
                        $row->admin_note ?: '-',
                        $row->rejection_note ?: '-',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function print(Request $request)
    {
        $orders = $this->filteredQuery($request)
            ->with(['studyProgram', 'paymentVerifiedBy'])
            ->latest()
            ->get();

        return view('admin.wisuda.orders.print', [
            'orders' => $orders,
            'summary' => $this->summary($request),
            'periodLabel' => $this->periodLabel($request),
            'generatedAt' => now(),
            'filters' => $request->all(),
        ]);
    }

    private function filteredQuery(Request $request)
    {
        $user = $request->user();
        $query = GraduationTogaOrder::query();

        if ($user && $user->isAdminJurusan() && $user->study_program_id) {
            $query->where('study_program_id', $user->study_program_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('period')) {
            match ($request->period) {
                'today' => $query->whereDate('created_at', today()),
                'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                'year' => $query->whereYear('created_at', now()->year),
                default => null,
            };
        }

        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%'.$request->order_code.'%');
        }
        if ($request->filled('student_name')) {
            $query->where('student_name', 'like', '%'.$request->student_name.'%');
        }
        if ($request->filled('nim')) {
            $query->where('nim', 'like', '%'.$request->nim.'%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }
        if ($request->filled('study_program_id')) {
            $query->where('study_program_id', $request->study_program_id);
        }
        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', (int) $request->graduation_year);
        }
        if ($request->filled('toga_size')) {
            $query->where('toga_size', $request->toga_size);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('proof_filter')) {
            if ($request->proof_filter === 'uploaded') {
                $query->whereNotNull('payment_proof_path');
            } elseif ($request->proof_filter === 'missing') {
                $query->whereNull('payment_proof_path');
            }
        }

        return $query;
    }

    private function summary(Request $request): array
    {
        $base = $this->filteredQuery($request);

        return [
            'total_orders' => (clone $base)->count(),
            'total_uploaded' => (clone $base)->whereNotNull('payment_proof_path')->count(),
            'total_verified' => (clone $base)->where('payment_status', 'verified')->count(),
            'total_rejected' => (clone $base)->where('payment_status', 'rejected')->count(),
        ];
    }

    private function periodLabel(Request $request): string
    {
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = $request->date_from ?: '...';
            $to = $request->date_to ?: '...';

            return "{$from} s.d. {$to}";
        }

        return match ($request->input('period')) {
            'today' => 'Hari ini ('.now()->format('d/m/Y').')',
            'week' => 'Minggu ini',
            'month' => 'Bulan ini ('.now()->translatedFormat('F Y').')',
            'year' => 'Tahun '.now()->year,
            default => 'Semua periode',
        };
    }

    private function studyProgramsForFilter(Request $request)
    {
        $user = $request->user();

        if ($user && $user->isAdminJurusan() && $user->study_program_id) {
            return StudyProgram::where('id', $user->study_program_id)->get();
        }

        return StudyProgram::orderBy('name')->get();
    }

    private function authorizeOrderAccess(GraduationTogaOrder $order): void
    {
        $user = auth()->user();

        if ($user && $user->isAdminJurusan() && $user->study_program_id
            && (int) $order->study_program_id !== (int) $user->study_program_id) {
            abort(403, 'Anda hanya dapat mengakses pesanan program studi Anda.');
        }
    }

    private function orderStatuses(): array
    {
        return [
            'pending_payment' => 'Menunggu Pembayaran',
            'payment_uploaded' => 'Bukti Diunggah',
            'payment_verified' => 'Pembayaran Terverifikasi',
            'payment_rejected' => 'Pembayaran Ditolak',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    private function paymentStatuses(): array
    {
        return [
            'unpaid' => 'Menunggu informasi pembayaran',
            'uploaded' => 'Menunggu verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
        ];
    }
}
