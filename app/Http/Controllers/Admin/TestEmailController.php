<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    public function __construct(private AdminActivityLogger $activityLogger) {}

    public function index()
    {
        return view('admin.test-email', [
            'mailer' => config('mail.default'),
            'fromAddress' => config('mail.from.address'),
            'fromName' => config('mail.from.name'),
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'recipient' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        try {
            Mail::raw($data['message'], function ($mail) use ($data) {
                $mail->to($data['recipient'])
                    ->subject($data['subject']);
            });

            $this->activityLogger->log(
                'test_email',
                "Kirim test email ke {$data['recipient']}",
            );

            $hint = config('mail.default') === 'log'
                ? ' Email dicatat di storage/logs/laravel.log (MAIL_MAILER=log).'
                : '';

            return back()->with('success', 'Test email berhasil dikirim.'.$hint);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengirim test email: '.$e->getMessage());
        }
    }
}
