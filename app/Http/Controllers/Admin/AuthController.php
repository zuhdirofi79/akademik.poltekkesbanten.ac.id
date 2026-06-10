<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private AdminActivityLogger $activityLogger) {}

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isStaff() && Auth::user()->is_active) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau password salah.');
        }

        $user = Auth::user();

        if (! $user->isStaff()) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Akun tidak memiliki akses admin.');
        }

        if (! $user->is_active) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Akun admin dinonaktifkan. Hubungi superadmin.');
        }

        $request->session()->regenerate();

        $this->activityLogger->log('login', 'Login admin berhasil.', $user);

        if ($user->must_change_password) {
            return redirect()->route('admin.change-password.show')
                ->with('warning', 'Password masih menggunakan password sementara. Segera ubah password.');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $this->activityLogger->log('logout', 'Logout admin.', $request->user());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
