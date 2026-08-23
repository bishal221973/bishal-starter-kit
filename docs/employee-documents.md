Employee Documents

The Bishal Starter Kit provides an employee document management structure through the employee_documents table.

Employee documents are associated with users and can be used to store documents such as:

Citizenship
Passport
Driving license
Employment contracts
Qualification certificates
Experience letters
Other employee-related documents
Employee Documents Table

Employee documents are stored in:

employee_documents

The table contains:

id
user_id
document_type
title
document_number
file_path
file_name
file_type
file_size
issued_at
expires_at
notes
created_at
updated_at
User Relationship

Every employee document belongs to a user.

The database relationship is:

users
  |
  └── employee_documents

The user_id column is a foreign key:

$table->foreignId('user_id')
    ->constrained()
    ->cascadeOnDelete();

This means that when a user is deleted, their associated employee documents are also deleted.

Document Type

The document_type field identifies the type of employee document.

Example values include:

citizenship
passport
driving_license
contract
qualification
experience_letter

The application can support additional document types as required.

Example:

$document->document_type = 'citizenship';
Document Title

The title field contains the display name of the document.

Example:

Nepali Citizenship Certificate
Bachelor Degree Certificate
Employment Contract
Experience Letter

Example:

$document->title = 'Nepali Citizenship Certificate';
Document Number

The document_number field stores the identification number associated with a document.

This field is optional.

Example:

$document->document_number = '12-34-56789';

Because some documents do not have a document number, the database allows this field to be NULL.

File Information

The employee document stores information about the uploaded file.

Available fields:

file_path
file_name
file_type
file_size
File Path

The file_path field stores the location of the uploaded file.

Example:

employee-documents/15/citizenship.pdf

The database stores the path rather than the complete public URL.

Example:

$document->file_path;
File Name

The file_name field stores the original or display filename.

Example:

citizenship-certificate.pdf

Example:

$document->file_name;
File Type

The file_type field stores the uploaded file's MIME type or file type.

Examples:

application/pdf
image/jpeg
image/png

Example:

$document->file_type = $request->file('document')->getMimeType();
File Size

The file_size field stores the size of the uploaded file in bytes.

The database column is:

$table->unsignedBigInteger('file_size')->nullable();

Example:

$document->file_size = $file->getSize();
Document Dates

Employee documents support two important dates:

issued_at
expires_at

Both fields are optional.

Issued Date

The issued_at field represents the date when the document was issued.

Example:

$document->issued_at = '2026-01-15';
Expiration Date

The expires_at field represents the date when the document expires.

Example:

$document->expires_at = '2031-01-15';

Some documents may not expire, so this field can be NULL.

Checking Document Expiration

Using Carbon, an expiring document can be checked with:

if ($document->expires_at && $document->expires_at->isPast()) {
    // Document has expired
}

To check whether a document is still valid:

if (
    $document->expires_at &&
    $document->expires_at->isFuture()
) {
    // Document is still valid
}
Document Notes

The notes field allows additional information to be stored.

Example:

Original document verified by HR.

Example:

$document->notes = 'Original document verified by HR.';
Model

A typical EmployeeDocument model can define the relationship to the user.

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'user_id',
        'document_type',
        'title',
        'document_number',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'issued_at',
        'expires_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
User Model Relationship

The User model can define the inverse relationship.

use Illuminate\Database\Eloquent\Relations\HasMany;

public function employeeDocuments(): HasMany
{
    return $this->hasMany(EmployeeDocument::class);
}

Then documents can be accessed through:

$user->employeeDocuments;
Creating a Document

A document can be created using Eloquent:

$document = EmployeeDocument::create([
    'user_id' => $user->id,
    'document_type' => 'citizenship',
    'title' => 'Citizenship Certificate',
    'document_number' => '12-34-56789',
    'file_path' => $path,
    'file_name' => $file->getClientOriginalName(),
    'file_type' => $file->getMimeType(),
    'file_size' => $file->getSize(),
    'issued_at' => $request->issued_at,
    'expires_at' => $request->expires_at,
    'notes' => $request->notes,
]);
Uploading a Document

Laravel's filesystem can be used to store uploaded employee documents.

Example:

$file = $request->file('document');

$path = $file->store('employee-documents', 'public');

Then store the generated path:

EmployeeDocument::create([
    'user_id' => $user->id,
    'document_type' => $request->document_type,
    'title' => $request->title,
    'document_number' => $request->document_number,
    'file_path' => $path,
    'file_name' => $file->getClientOriginalName(),
    'file_type' => $file->getMimeType(),
    'file_size' => $file->getSize(),
    'issued_at' => $request->issued_at,
    'expires_at' => $request->expires_at,
    'notes' => $request->notes,
]);
Validation

A typical document upload validation could be:

$request->validate([
    'document_type' => ['required', 'string', 'max:255'],
    'title' => ['required', 'string', 'max:255'],
    'document_number' => ['nullable', 'string', 'max:255'],
    'document' => [
        'required',
        'file',
        'max:10240',
    ],
    'issued_at' => ['nullable', 'date'],
    'expires_at' => ['nullable', 'date'],
    'notes' => ['nullable', 'string'],
]);

The max:10240 rule allows a maximum file size of approximately 10 MB.

Adjust the limit according to the application's requirements.

Supported Document Examples

A typical employee document list can include:

Document Type	Example
citizenship	Citizenship certificate
passport	Passport
driving_license	Driving license
contract	Employment contract
qualification	Academic qualification
experience_letter	Previous employment experience
other	Other employee document
Displaying Documents

Documents can be loaded with the employee:

$user = User::with('employeeDocuments')
    ->findOrFail($userId);

Then:

$user->employeeDocuments;
Downloading a Document

If files are stored on Laravel's public disk:

return Storage::disk('public')
    ->download($document->file_path);

Make sure the user has permission to access the document before returning it.

Viewing a Document

For a public storage file:

$url = Storage::disk('public')
    ->url($document->file_path);

Example:

return response()->json([
    'url' => Storage::disk('public')
        ->url($document->file_path),
]);

For sensitive employee documents, prefer a protected download/view route rather than exposing the storage URL directly.

Deleting a Document

When deleting a document, remove both the database record and the stored file.

Storage::disk('public')
    ->delete($document->file_path);

$document->delete();
Employee Document Controller

A basic controller structure:

<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'document_type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'document' => ['required', 'file', 'max:10240'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $file = $request->file('document');

        $path = $file->store(
            'employee-documents',
            'public'
        );

        EmployeeDocument::create([
            'user_id' => $validated['user_id'],
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'document_number' => $validated['document_number'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'issued_at' => $validated['issued_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with(
            'success',
            'Employee document uploaded successfully.'
        );
    }
}
Security

Employee documents can contain sensitive information.

Access should therefore be restricted to authorized users.

Before allowing a document to be downloaded, verify:

Authenticated user
+
Organization access
+
Employee access
+
Required permission

Do not allow users to download arbitrary files by providing another user's document ID.

Avoid insecure implementations such as:

return Storage::download(
    request('path')
);

Instead, locate the document through an authorized database record:

$document = EmployeeDocument::findOrFail($id);

Then verify authorization before returning the file.

Organization Considerations

The employee_documents table currently contains:

user_id

but does not directly contain:

organization_id

Employee organization membership is represented through the organization_user relationship.

Therefore, when retrieving employee documents in a multi-organization application, authorization should verify that the requested user belongs to the current organization.

Example concept:

$user->organizations()
    ->where('organizations.id', $organizationId)
    ->exists();

The exact implementation should follow the application's organization and authorization logic.

Storage Link

If documents are stored on Laravel's public disk, create the storage link:

php artisan storage:link

This creates:

public/storage

pointing to:

storage/app/public
Database Structure

The migration for employee documents is conceptually:

Schema::create('employee_documents', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('document_type');
    $table->string('title');
    $table->string('document_number')->nullable();

    $table->string('file_path');
    $table->string('file_name')->nullable();
    $table->string('file_type')->nullable();
    $table->unsignedBigInteger('file_size')->nullable();

    $table->date('issued_at')->nullable();
    $table->date('expires_at')->nullable();

    $table->text('notes')->nullable();

    $table->timestamps();
});
Summary

The employee document system provides:

Employee document types
Document titles
Document numbers
File storage
Original filenames
MIME types
File sizes
Issue dates
Expiration dates
Notes
User relationships
Automatic deletion when the associated user is deleted

The main database table is:

employee_documents

and the primary relationship is:

User
  └── hasMany(EmployeeDocument)