<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Services\AdminActivityLogger;

class ChangePasswordController extends Controller
{
    public function __construct(private AdminActivityLogger $activityLogger) {}

    public function show()
    {
        return view('admin.change-password');
    }

    public function update(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $user->update([
            'password' => $request->input('password'),
            'must_change_password' => false,
        ]);

        $this->activityLogger->log('change_password', 'Password akun diperbarui.', $user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
