<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use Illuminate\Http\Request;

class StudyProgramController extends Controller
{
    public function index()
    {
        return view('admin.study-programs.index', [
            'studyPrograms' => StudyProgram::orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.study-programs.form', ['studyProgram' => new StudyProgram()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:study_programs,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        StudyProgram::create([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.study-programs.index')
            ->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function edit(StudyProgram $studyProgram)
    {
        return view('admin.study-programs.form', compact('studyProgram'));
    }

    public function update(Request $request, StudyProgram $studyProgram)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:study_programs,name,'.$studyProgram->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $studyProgram->update([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.study-programs.index')
            ->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroy(StudyProgram $studyProgram)
    {
        if ($studyProgram->submissions()->exists()) {
            return back()->with('error', 'Program studi tidak dapat dihapus karena sudah memiliki pengajuan.');
        }

        $studyProgram->delete();

        return redirect()->route('admin.study-programs.index')
            ->with('success', 'Program studi berhasil dihapus.');
    }
}
