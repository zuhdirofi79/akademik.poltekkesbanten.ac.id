@extends('layouts.admin')

@section('title', 'Manajemen Admin')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <h1>Manajemen Admin</h1>
    <a href="{{ route('admin.users.create') }}" class="btn-sm btn-teal">Tambah Admin</a>
</div>

<div class="admin-card" style="margin-top:1rem;">
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Wajib Ganti Password</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td>{{ $user->must_change_password ? 'Ya' : 'Tidak' }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-sm btn-blue">Edit</a>
                        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm btn-gray" onclick="return confirm('Reset password akun ini?')">Reset Password</button>
                        </form>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-sm {{ $user->is_active ? 'btn-red' : 'btn-green' }}">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Hapus akun ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-red">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
