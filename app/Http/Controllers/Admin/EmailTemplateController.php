<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        return view('admin.email-templates.index', [
            'templates' => EmailTemplate::orderBy('name')->get(),
        ]);
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.form', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $emailTemplate->update([
            'name' => $data['name'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template email berhasil diperbarui.');
    }
}
