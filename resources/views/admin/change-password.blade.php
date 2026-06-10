@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('content')
<h1>Ganti Password</h1>

<div class="admin-card" style="max-width:520px;">
    @if(auth()->user()->must_change_password)
        <div class="alert alert-error">Anda wajib mengganti password sementara sebelum melanjutkan.</div>
    @endif

    <form method="POST" action="{{ route('admin.change-password.update') }}">
        @csrf

        @if(!auth()->user()->must_change_password)
            <div class="form-group">
                <label for="current_password">Password Saat Ini</label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
                @error('current_password')<small style="color:#dc2626;">{{ $message }}</small>@enderror
            </div>
        @endif

        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password" name="password" id="password" class="form-control" required minlength="8">
            @error('password')<small style="color:#dc2626;">{{ $message }}</small>@enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8">
        </div>

        <button type="submit" class="btn-sm btn-teal">Simpan Password</button>
        @if(!auth()->user()->must_change_password)
            <a href="{{ route('admin.dashboard') }}" class="btn-sm btn-gray">Batal</a>
        @endif
    </form>
</div>
@endsection
