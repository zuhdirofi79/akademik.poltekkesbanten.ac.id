@extends('layouts.admin')

@section('title', 'Program Studi')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;">
    <h1>Program Studi</h1>
    <a href="{{ route('admin.study-programs.create') }}" class="btn-sm btn-teal">Tambah Prodi</a>
</div>

<div class="admin-card" style="margin-top:1rem;">
    <table class="table">
        <thead>
            <tr><th>Nama</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            @foreach($studyPrograms as $program)
                <tr>
                    <td>{{ $program->name }}</td>
                    <td>{{ $program->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td>
                        <a href="{{ route('admin.study-programs.edit', $program) }}" class="btn-sm btn-blue">Edit</a>
                        <form method="POST" action="{{ route('admin.study-programs.destroy', $program) }}" style="display:inline;" onsubmit="return confirm('Hapus program studi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm btn-red">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $studyPrograms->links() }}
</div>
@endsection
