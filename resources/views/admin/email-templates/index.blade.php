@extends('layouts.admin')

@section('title', 'Template Email')

@section('content')
<h1>Template Email</h1>
<p>Gunakan placeholder: <code>{{ '{' }}{{ 'nama' }}{{ '}' }}</code>, <code>{{ '{' }}{{ 'layanan' }}{{ '}' }}</code>, <code>{{ '{' }}{{ 'tiket' }}{{ '}' }}</code>, <code>{{ '{' }}{{ 'token' }}{{ '}' }}</code>, <code>{{ '{' }}{{ 'link_progres' }}{{ '}' }}</code></p>

<div class="admin-card">
    <table class="table">
        <thead>
            <tr><th>Nama</th><th>Subject</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
                <tr>
                    <td>{{ $template->name }}</td>
                    <td>{{ $template->subject }}</td>
                    <td>{{ $template->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td><a href="{{ route('admin.email-templates.edit', $template) }}" class="btn-sm btn-blue">Edit</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
