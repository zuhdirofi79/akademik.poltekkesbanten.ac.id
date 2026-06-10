@extends('layouts.admin')

@section('title', 'Test Email')

@section('content')
<h1>Test Email</h1>

<div class="admin-card" style="max-width:640px;">
    <p><strong>Mailer aktif:</strong> {{ $mailer }}</p>
    <p><strong>From:</strong> {{ $fromName }} &lt;{{ $fromAddress }}&gt;</p>

    @if($mailer === 'log')
        <div class="alert alert-info">
            Mode <code>MAIL_MAILER=log</code> tidak mengirim ke inbox. Cek <code>storage/logs/laravel.log</code>.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.test-email.send') }}">
        @csrf
        <div class="form-group">
            <label for="recipient">Email Penerima</label>
            <input type="email" name="recipient" id="recipient" class="form-control" value="{{ old('recipient', auth()->user()->email) }}" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject', 'Test Email - Layanan Akademik Poltekkes Kemenkes Banten') }}" required>
        </div>
        <div class="form-group">
            <label for="message">Pesan</label>
            <textarea name="message" id="message" class="form-control" rows="6" required>{{ old('message', 'Ini adalah email uji dari sistem Layanan Akademik Poltekkes Kemenkes Banten.') }}</textarea>
        </div>
        <button type="submit" class="btn-sm btn-teal">Kirim Test Email</button>
    </form>
</div>
@endsection
