<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct(private AdminActivityLogger $activityLogger) {}

    public function index()
    {
        return view('admin.users.index', [
            'users' => User::whereIn('role', ['admin', 'superadmin'])
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    public function store(StoreUserRequest $request)
    {
        $plainPassword = $request->filled('password')
            ? $request->input('password')
            : Str::password(12);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'password' => $plainPassword,
            'must_change_password' => true,
            'is_active' => true,
        ]);

        $this->activityLogger->log(
            'create_admin',
            "Menambah akun {$user->role}: {$user->email}",
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun admin berhasil ditambahkan.')
            ->with('generated_password', $request->filled('password') ? null : $plainPassword)
            ->with('generated_email', $user->email);
    }

    public function edit(User $user)
    {
        abort_unless($user->isStaff(), 404);

        return view('admin.users.form', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_unless($user->isStaff(), 404);

        $user->update($request->only('name', 'email', 'role'));

        $this->activityLogger->log(
            'update_admin',
            "Memperbarui akun {$user->email} (role: {$user->role})",
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_unless($user->isStaff(), 404);

        $plainPassword = Str::password(12);

        $user->update([
            'password' => $plainPassword,
            'must_change_password' => true,
        ]);

        $this->activityLogger->log(
            'reset_password_admin',
            "Reset password akun {$user->email}",
        );

        return back()
            ->with('success', 'Password berhasil direset.')
            ->with('generated_password', $plainPassword)
            ->with('generated_email', $user->email);
    }

    public function toggleActive(Request $request, User $user)
    {
        abort_unless($user->isStaff(), 404);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->isSuperAdmin() && $user->is_active) {
            $activeSuperadmins = User::where('role', 'superadmin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeSuperadmins < 1) {
                return back()->with('error', 'Minimal harus ada satu superadmin aktif.');
            }
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        $this->activityLogger->log(
            'toggle_admin_active',
            "Akun {$user->email} {$status}",
        );

        return back()->with('success', "Akun berhasil {$status}.");
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($user->isStaff(), 404);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->isSuperAdmin()) {
            $activeSuperadmins = User::where('role', 'superadmin')
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeSuperadmins < 1) {
                return back()->with('error', 'Minimal harus ada satu superadmin aktif.');
            }
        }

        $email = $user->email;
        $user->delete();

        $this->activityLogger->log('delete_admin', "Menghapus akun {$email}");

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun admin berhasil dihapus.');
    }
}
