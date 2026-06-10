@extends('layouts.admin')

@section('title', 'Edit Template Email')

@section('content')
<h1>Edit Template: {{ $emailTemplate->name }}</h1>

<div class="admin-card">
    <form method="POST" action="{{ route('admin.email-templates.update', $emailTemplate) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Template</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $emailTemplate->name) }}" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject Email</label>
            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject', $emailTemplate->subject) }}" required>
        </div>
        <div class="form-group">
            <label for="body">Isi Template (ringkas)</label>
            <textarea name="body" id="body" class="form-control" rows="8" required>{{ old('body', $emailTemplate->body) }}</textarea>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $emailTemplate->is_active))>
                Aktif
            </label>
        </div>
        <button type="submit" class="btn-sm btn-teal">Simpan</button>
        <a href="{{ route('admin.email-templates.index') }}" class="btn-sm btn-gray">Batal</a>
    </form>
</div>
@endsection
