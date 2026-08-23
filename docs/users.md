Users

The Bishal Starter Kit uses Laravel's authentication system with Laravel Jetstream and supports organization-based user management.

Users are stored in the users table, while organization-specific employee information is stored in the organization_user table.

User Table

The users table contains the core authentication information.

Main fields:

Field	Description
id	Unique user ID
name	User's name
email	Unique login email
email_verified_at	Email verification timestamp
password	Hashed password
remember_token	Remember-me token
current_team_id	Current team ID
profile_photo_path	Profile photo
password_created_at	Date password was created
password_expired_at	Password expiration date
login_attempts	Number of failed login attempts
locked_until	Account lockout timestamp
deleted_at	Soft-delete timestamp

The application uses soft deletes for users.

Creating Users

Users can be created through the application's user-management interface.

A typical user creation process is:

Create User
    ↓
Name
Email
Password
    ↓
Assign Organization
    ↓
Assign Role
    ↓
Set Employee Information

Passwords must always be hashed before being stored.

Example:

use Illuminate\Support\Facades\Hash;

$user->password = Hash::make($request->password);
User Authentication

Authentication is provided by Laravel Jetstream.

Users can:

Register
Login
Logout
Reset passwords
Change passwords
Verify email
Manage their profile
Manage authentication sessions

See authentication.md for detailed authentication documentation.

User Roles

Users can be assigned roles using Spatie Laravel Permission.

Example:

$user->assignRole('admin');

Check a role:

$user->hasRole('admin');

Check a permission:

$user->can('users.view');

See rolepermission.md for detailed role and permission documentation.

User Organizations

A user can be associated with an organization through the organization_user pivot table.

The relationship contains additional employee information such as:

employee_code
gender
date_of_birth
personal_email
personal_phone
address
city
state
country
postal_code
employee_type
department
designation
joined_at
probation_ends_at
employment_ends_at
salary
salary_type
is_active
can_login

This allows the same user authentication record to contain organization-specific employee information.

Organization User Relationship

The relationship is:

User
 │
 ├── Organization
 │       │
 │       └── Employee information
 │
 └── Roles / Permissions

The pivot table contains:

organization_id
user_id

with a unique constraint:

$table->unique([
    'organization_id',
    'user_id',
]);

Therefore, the same user cannot have duplicate memberships in the same organization.

Employee Code

Each organization can have its own employee code.

The pivot table has a unique constraint:

$table->unique([
    'organization_id',
    'employee_code',
]);

This means employee codes must be unique within an organization.

For example:

Organization A
EMP-001
EMP-002

Organization B
EMP-001
EMP-002

The same employee code can exist in different organizations.

Active Users

The user itself does not contain an is_active column.

Organization-specific activation is handled through:

organization_user.is_active

Example:

$employee->is_active = true;

This allows an employee to be active in one organization while having a different membership status in another organization.

Login Permission

The organization membership contains:

can_login

This determines whether the employee is allowed to log into the application through that organization.

For example:

$employee->can_login = true;

To disable login:

$employee->can_login = false;
Password Management

The user table tracks password lifecycle information.

Password Created Date
password_created_at

This records when the user's password was created.

Password Expiration
password_expired_at

can be used to determine whether a user's password has expired.

Password expiration behavior is controlled by the application's password policy configuration.

See passwordpolicy.md.

Login Attempts

The user table contains:

login_attempts
locked_until

These fields support login security and account lockout.

Example:

Failed login
     ↓
login_attempts++
     ↓
Maximum attempts reached
     ↓
locked_until set
     ↓
Login temporarily blocked

Login security settings are configurable through the application's configuration system.

Soft Deleted Users

Users use Laravel's SoftDeletes.

The database contains:

deleted_at

Deleting a user normally does not immediately remove the database record.

Example:

$user->delete();

The user will then have a deleted_at timestamp.

Normal queries automatically exclude soft-deleted users.

To include deleted users:

User::withTrashed()->get();

To retrieve only deleted users:

User::onlyTrashed()->get();
Restoring a User

A soft-deleted user can be restored:

$user->restore();

Or:

User::withTrashed()
    ->find($id)
    ->restore();
Permanently Deleting a User

If permanent deletion is intentionally required:

$user->forceDelete();

Use permanent deletion carefully because the user record cannot be recovered normally afterward.

Profile Photo

The user table contains:

profile_photo_path

which stores the user's profile photo path.

If profile images are stored using Laravel's public storage disk, make sure the storage link exists:

php artisan storage:link
Email Verification

The user table contains:

email_verified_at

Laravel Jetstream can use this field to determine whether the user's email address has been verified.

A verified user has a timestamp in:

email_verified_at

An unverified user has:

NULL
Sessions

The application uses a sessions table containing:

id
user_id
ip_address
user_agent
payload
last_activity

This allows the application to track authenticated sessions.

Session behavior is also used by the application's session-management and security features.

See:

session-management.md
security.md
User Deactivation

User access can be restricted at the organization level by setting:

can_login = false

or:

is_active = false

in the organization_user record.

This is preferable to deleting the user when the employee should retain their historical information.

User and Employee Documents

Employee documents are associated with the user through:

employee_documents.user_id

Documents can include:

Citizenship
Passport
Driving License
Contract
Qualification
Experience Letter

See employee-doc.md for more information.

User Model Relationships

A typical User model can contain relationships such as:

public function organizations()
{
    return $this->belongsToMany(
        Organization::class,
        'organization_user'
    );
}

Employee documents:

public function employeeDocuments()
{
    return $this->hasMany(EmployeeDocument::class);
}

Roles and permissions are provided by Spatie Laravel Permission.

Querying Users

Get all users:

$users = User::all();

Find a user:

$user = User::find($id);

Find by email:

$user = User::where('email', $email)->first();

Get active organization memberships:

$users = User::whereHas('organizations', function ($query) {
    $query->where('is_active', true);
})->get();
Current User

The currently authenticated user can be accessed with:

$user = auth()->user();

Or:

$user = request()->user();

Check authentication:

if (auth()->check()) {
    // User is authenticated
}
Authorization

Before allowing users to perform an action, use roles or permissions.

Example:

if ($user->can('users.create')) {
    // Create user
}

For route middleware:

Route::middleware([
    'auth',
    'permission:users.view',
])->group(function () {
    // User routes
});
Recommended User Lifecycle

The recommended lifecycle is:

Create User
    ↓
Verify Email
    ↓
Assign Organization
    ↓
Assign Employee Information
    ↓
Assign Role
    ↓
Grant Permissions Through Role
    ↓
User Can Login
    ↓
Employee Works
    ↓
Deactivate / Disable Login
    ↓
Keep Historical Data

Avoid permanently deleting users unless the data must genuinely be removed.

Related Documentation

For additional user functionality, see:

authentication.md
employees.md
employee-doc.md
organizations.md
rolepermission.md
passwordpolicy.md
session-management.md
security.md
autologout.md