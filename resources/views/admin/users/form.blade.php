@extends('layouts.admin')

@section('title', $user->exists ? 'Edit Admin' : 'Tambah Admin')

@section('content')
<h1>{{ $user->exists ? 'Edit' : 'Tambah' }} Admin</h1>

<div class="admin-card" style="max-width:560px;">
    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user->exists) @method('PATCH') @endif

        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name')<small style="color:#dc2626;">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            @error('email')<small style="color:#dc2626;">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-select" required>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                <option value="superadmin" @selected(old('role', $user->role) === 'superadmin')>Superadmin</option>
            </select>
            @error('role')<small style="color:#dc2626;">{{ $message }}</small>@enderror
        </div>

        @if(!$user->exists)
            <div class="form-group">
                <label for="password">Password (opsional, min 8 karakter)</label>
                <input type="password" name="password" id="password" class="form-control" minlength="8">
                <small style="color:#6b7280;">Kosongkan untuk generate password sementara otomatis.</small>
                @error('password')<small style="color:#dc2626;">{{ $message }}</small>@enderror
            </div>
        @endif

        <button type="submit" class="btn-sm btn-teal">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="btn-sm btn-gray">Batal</a>
    </form>
</div>
@endsection
