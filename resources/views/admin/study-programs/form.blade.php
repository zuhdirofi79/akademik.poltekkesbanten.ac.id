@extends('layouts.admin')

@section('title', $studyProgram->exists ? 'Edit Program Studi' : 'Tambah Program Studi')

@section('content')
<h1>{{ $studyProgram->exists ? 'Edit' : 'Tambah' }} Program Studi</h1>

<div class="admin-card">
    <form method="POST" action="{{ $studyProgram->exists ? route('admin.study-programs.update', $studyProgram) : route('admin.study-programs.store') }}">
        @csrf
        @if($studyProgram->exists) @method('PUT') @endif

        <div class="form-group">
            <label for="name">Nama Program Studi</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $studyProgram->name) }}" required>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $studyProgram->is_active ?? true))>
                Aktif
            </label>
        </div>
        <button type="submit" class="btn-sm btn-teal">Simpan</button>
        <a href="{{ route('admin.study-programs.index') }}" class="btn-sm btn-gray">Batal</a>
    </form>
</div>
@endsection
