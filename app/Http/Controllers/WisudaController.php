<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGraduationTogaOrderRequest;
use App\Http\Requests\UploadWisudaPaymentProofRequest;
use App\Models\GraduationTogaOrder;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WisudaController extends Controller
{
    public function index()
    {
        return view('wisuda.index', [
            'config' => config('wisuda'),
            'studyPrograms' => StudyProgram::active()->orderBy('name')->get(),
            'captcha' => $this->generateCaptcha(),
        ]);
    }

    public function createOrder(Request $request)
    {
        $prodiHint = strtolower((string) $request->query('prodi', ''));

        return view('wisuda.order', [
            'studyPrograms' => StudyProgram::active()->orderBy('name')->get(),
            'captcha' => $this->generateCaptcha(),
            'config' => config('wisuda'),
            'prodiHint' => $prodiHint,
        ]);
    }

    public function storeOrder(StoreGraduationTogaOrderRequest $request)
    {
        $data = $request->validated();
        $program = StudyProgram::findOrFail($data['study_program_id']);

        $order = GraduationTogaOrder::create([
            'order_code' => GraduationTogaOrder::generateOrderCode(),
            'student_name' => $data['student_name'],
            'nim' => $data['nim'],
            'nik' => $data['nik'] ?? null,
            'study_program_id' => $program->id,
            'study_program_name' => $program->name,
            'graduation_year' => (int) $data['graduation_year'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'toga_size' => 'All Size',
            'size_note' => $data['size_note'] ?? null,
            'quantity' => $data['quantity'],
            'status' => 'pending_payment',
            'payment_status' => 'unpaid',
            'public_token' => GraduationTogaOrder::generatePublicToken(),
        ]);

        return redirect()
            ->route('wisuda.order.show', [
                'orderCode' => $order->order_code,
                'token' => $order->public_token,
            ])
            ->with('success', 'Pesanan toga wisuda berhasil dibuat. Untuk informasi rekening, hubungi Tim Sekretariat DWP Poltekkes Kemenkes Banten lalu unggah bukti pembayaran.');
    }

    public function showOrder(Request $request, string $orderCode)
    {
        $order = $this->findPublicOrder($request, $orderCode);

        return view('wisuda.show', [
            'order' => $order,
            'config' => config('wisuda'),
        ]);
    }

    public function payment(Request $request, string $orderCode)
    {
        $order = $this->findPublicOrder($request, $orderCode);

        return view('wisuda.payment', [
            'order' => $order,
            'config' => config('wisuda'),
        ]);
    }

    public function uploadPaymentProof(UploadWisudaPaymentProofRequest $request, string $orderCode)
    {
        $order = $this->findPublicOrder($request, $orderCode);

        if (in_array($order->status, ['cancelled', 'completed'], true)) {
            return back()->with('error', 'Pesanan ini tidak dapat diunggah bukti pembayarannya.');
        }

        if ($order->payment_status === 'verified') {
            return back()->with('error', 'Pembayaran sudah diverifikasi. Tidak perlu mengunggah ulang.');
        }

        $file = $request->file('payment_proof');
        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid()->toString().'.'.$extension;
        $directory = 'wisuda/payment-proofs/'.$order->id;
        $path = $directory.'/'.$storedName;

        if ($order->payment_proof_path && Storage::disk('local')->exists($order->payment_proof_path)) {
            Storage::disk('local')->delete($order->payment_proof_path);
        }

        Storage::disk('local')->putFileAs($directory, $file, $storedName);

        $order->update([
            'payment_proof_path' => $path,
            'payment_proof_original_name' => $file->getClientOriginalName(),
            'payment_proof_mime' => $file->getMimeType() ?? 'application/octet-stream',
            'payment_proof_size' => $file->getSize(),
            'payment_uploaded_at' => now(),
            'payment_status' => 'uploaded',
            'status' => 'payment_uploaded',
            'payment_rejected_at' => null,
            'payment_rejected_by' => null,
            'rejection_note' => null,
        ]);

        return redirect()
            ->route('wisuda.payment', [
                'orderCode' => $order->order_code,
                'token' => $order->public_token,
            ])
            ->with('success', 'Bukti pembayaran berhasil diunggah. Tim Sekretariat DWP Poltekkes Kemenkes Banten akan melakukan verifikasi.');
    }

    private function findPublicOrder(Request $request, string $orderCode): GraduationTogaOrder
    {
        $order = GraduationTogaOrder::where('order_code', $orderCode)->firstOrFail();
        $token = (string) $request->query('token', $request->input('token', ''));

        if ($order->public_token && $token !== '' && ! hash_equals($order->public_token, $token)) {
            abort(403, 'Token akses pesanan tidak valid.');
        }

        if ($order->public_token && $token === '') {
            abort(403, 'Token akses pesanan diperlukan.');
        }

        return $order;
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
