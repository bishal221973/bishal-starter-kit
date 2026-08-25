<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentControlle extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],

            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:5120',
            ],

            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $file = $request->file('file');

        $path = $file->store(
            'employee-documents',
            'public'
        );

        EmployeeDocument::create([
            'user_id' => auth()->id(),
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'document_number' => $validated['document_number'] ?? null,

            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),

            'issued_at' => $validated['issued_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with(
            'success',
            'Document uploaded successfully.'
        );
    }

    public function update(Request $request, EmployeeDocument $employeeDocument)
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],

            'file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:5120',
            ],

            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $employeeDocument->update([
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'document_number' => $validated['document_number'] ?? null,
            'issued_at' => $validated['issued_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->hasFile('file')) {

            if ($employeeDocument->file_path) {
                Storage::disk('public')->delete(
                    $employeeDocument->file_path
                );
            }

            $file = $request->file('file');

            $path = $file->store(
                'employee-documents',
                'public'
            );

            $employeeDocument->update([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return back()->with(
            'success',
            'Document updated successfully.'
        );
    }

    public function destroy(EmployeeDocument $employeeDocument)
    {
        if ($employeeDocument->file_path) {
            Storage::disk('public')->delete(
                $employeeDocument->file_path
            );
        }

        $employeeDocument->delete();

        return back()->with(
            'success',
            'Document deleted successfully.'
        );
    }
}
