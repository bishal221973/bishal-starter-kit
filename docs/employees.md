Employees

The Bishal Starter Kit provides employee management through the relationship between the users, organizations, and organization_user tables.

An employee is represented by a user associated with an organization. Employee-specific information is stored in the organization_user pivot table.

This structure allows the same user account to be associated with an organization while keeping employment information separate from the user's authentication information.

Employee Structure

The employee system uses:

users
   |
   | user_id
   |
organization_user
   |
   | organization_id
   |
organizations

The main employee information is stored in:

organization_user

while authentication information is stored in:

users
Users vs Employees

The users table contains authentication and account information.

Examples:

name
email
password
email_verified_at
profile_photo_path
password_created_at
password_expired_at
login_attempts
locked_until

Employee-specific information is stored in organization_user.

Examples:

employee_code
gender
date_of_birth
personal_email
personal_phone
department
designation
joined_at
probation_ends_at
employment_ends_at
salary
salary_type

This separation keeps authentication data independent from organization-specific employment information.

Organization Employee Relationship

The organization_user table connects users and organizations.

organizations
      |
      | many-to-many
      |
    users

The relationship also contains additional employee information.

The migration uses:

$table->foreignId('organization_id')
    ->nullable()
    ->constrained()
    ->nullOnDelete();

$table->foreignId('user_id')
    ->constrained()
    ->cascadeOnDelete();
Employee Code

Each employee can have an employee code.

Database field:

employee_code

Example:

EMP-001
EMP-002
EMP-003

The migration defines a unique constraint for the organization and employee code:

$table->unique([
    'organization_id',
    'employee_code',
]);

Therefore, an employee code can be unique within an organization.

Employee Gender

The employee record supports:

male
female
other

Database field:

gender

Example:

$employee->gender;

The field is nullable.

Date of Birth

Employee date of birth is stored in:

date_of_birth

Example:

$employee->date_of_birth;

The database column is:

$table->date('date_of_birth')->nullable();

For date formatting and calendar configuration, see:

Date and Time

Personal Contact Information

Employees can have personal contact information.

Available fields:

personal_email
personal_phone

Example:

$employee->personal_email;
$employee->personal_phone;

These are separate from the primary user's account email.

Employee Address

The employee record supports:

address
city
state
country
postal_code

Example:

address
city
state
country
postal_code

All of these fields are optional.

Employment Type

The employee record contains:

employee_type

The default value is:

full_time

The intended values include:

full_time
part_time
contract
intern

Example:

$employee->employee_type;
Department

The employee's department is stored in:

department

Example:

IT
Human Resources
Finance
Administration
Sales

Example:

$employee->department = 'IT';
Designation

The employee's job designation is stored in:

designation

Examples:

Software Developer
Project Manager
Accountant
HR Officer
System Administrator

Example:

$employee->designation = 'Software Developer';
Employment Dates

The employee record supports several employment-related dates:

joined_at
probation_ends_at
employment_ends_at
Joined Date
joined_at

Stores the employee's joining date.

Probation End Date
probation_ends_at

Stores the date when the employee's probation period ends.

Employment End Date
employment_ends_at

Stores the date when the employee's employment ends.

All three fields are nullable.

Salary

Employee salary information is stored in:

salary
salary_type

The salary column is:

$table->decimal('salary', 12, 2)->nullable();

The default salary type is:

monthly

Supported intended values include:

monthly
yearly
hourly

Example:

salary = 50000
salary_type = monthly
Emergency Contact

Employee emergency contact information includes:

emergency_contact_name
emergency_contact_phone
emergency_contact_relation

Example:

emergency_contact_name = Ram Chaudhary
emergency_contact_phone = 98XXXXXXXX
emergency_contact_relation = Brother

These fields are optional.

Identification

The employee record supports identification information:

national_id
tax_number

Example:

$employee->national_id;
$employee->tax_number;

These values should be protected appropriately because they can contain sensitive personal information.

Employee Status

The employee record contains:

is_active

The default value is:

true

Example:

if ($employee->is_active) {
    // Employee is active
}

Deactivate an employee:

$employee->is_active = false;
$employee->save();
Can Login

Employees have a separate login permission:

can_login

The default is:

true

This allows an organization to have an employee record without necessarily allowing that employee to log into the application.

Example:

if ($employee->can_login) {
    // Employee can access the application
}

Disable login:

$employee->can_login = false;
$employee->save();
Employee Documents

Employees can have documents stored in the employee_documents table.

The document relationship is based on:

employee_documents.user_id

Examples:

Citizenship
Passport
Driving License
Employment Contract
Qualification Certificate
Experience Letter

See:

Employee Documents

Employee Model

Because employee information is stored in the organization_user table, it is typically accessed through the organization/user relationship.

For example, the User model can define:

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

public function organizations(): BelongsToMany
{
    return $this->belongsToMany(
        Organization::class,
        'organization_user'
    )->withPivot([
        'employee_code',
        'gender',
        'date_of_birth',
        'personal_email',
        'personal_phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'employee_type',
        'department',
        'designation',
        'joined_at',
        'probation_ends_at',
        'employment_ends_at',
        'salary',
        'salary_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'national_id',
        'tax_number',
        'is_active',
        'can_login',
    ]);
}
Organization Model

The Organization model can define the inverse relationship:

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

public function users(): BelongsToMany
{
    return $this->belongsToMany(
        User::class,
        'organization_user'
    )->withPivot([
        'employee_code',
        'gender',
        'date_of_birth',
        'personal_email',
        'personal_phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'employee_type',
        'department',
        'designation',
        'joined_at',
        'probation_ends_at',
        'employment_ends_at',
        'salary',
        'salary_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'national_id',
        'tax_number',
        'is_active',
        'can_login',
    ]);
}
Creating an Employee

An employee can be created by creating a user and attaching that user to an organization with employee information.

Example:

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

Attach the employee to an organization:

$organization->users()->attach($user->id, [
    'employee_code' => $request->employee_code,
    'gender' => $request->gender,
    'date_of_birth' => $request->date_of_birth,
    'personal_email' => $request->personal_email,
    'personal_phone' => $request->personal_phone,
    'address' => $request->address,
    'city' => $request->city,
    'state' => $request->state,
    'country' => $request->country,
    'postal_code' => $request->postal_code,
    'employee_type' => $request->employee_type,
    'department' => $request->department,
    'designation' => $request->designation,
    'joined_at' => $request->joined_at,
    'probation_ends_at' => $request->probation_ends_at,
    'salary' => $request->salary,
    'salary_type' => $request->salary_type,
    'is_active' => true,
    'can_login' => true,
]);
Updating Employee Information

Employee information stored in the pivot table can be updated using updateExistingPivot().

Example:

$organization->users()->updateExistingPivot(
    $user->id,
    [
        'department' => 'IT',
        'designation' => 'Senior Developer',
        'salary' => 75000,
        'salary_type' => 'monthly',
    ]
);
Getting Employee Information

Load an organization's employees:

$employees = $organization->users;

Employee-specific information is available through the pivot:

foreach ($employees as $employee) {
    echo $employee->name;
    echo $employee->pivot->employee_code;
    echo $employee->pivot->designation;
}
Getting Active Employees

You can filter employees based on the pivot field:

$employees = $organization->users()
    ->wherePivot('is_active', true)
    ->get();
Getting Employees Who Can Login
$employees = $organization->users()
    ->wherePivot('can_login', true)
    ->get();
Getting Employees by Department

Example:

$employees = $organization->users()
    ->wherePivot('department', 'IT')
    ->get();
Getting Employees by Employee Type

Example:

$employees = $organization->users()
    ->wherePivot('employee_type', 'full_time')
    ->get();
Employee Validation

A typical employee form can use validation such as:

$request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'max:255'],

    'employee_code' => [
        'nullable',
        'string',
        'max:255',
    ],

    'gender' => [
        'nullable',
        'in:male,female,other',
    ],

    'date_of_birth' => [
        'nullable',
        'date',
    ],

    'personal_email' => [
        'nullable',
        'email',
    ],

    'personal_phone' => [
        'nullable',
        'string',
        'max:50',
    ],

    'employee_type' => [
        'required',
        'in:full_time,part_time,contract,intern',
    ],

    'department' => [
        'nullable',
        'string',
        'max:255',
    ],

    'designation' => [
        'nullable',
        'string',
        'max:255',
    ],

    'joined_at' => [
        'nullable',
        'date',
    ],

    'probation_ends_at' => [
        'nullable',
        'date',
    ],

    'employment_ends_at' => [
        'nullable',
        'date',
    ],

    'salary' => [
        'nullable',
        'numeric',
        'min:0',
    ],

    'salary_type' => [
        'required',
        'in:monthly,yearly,hourly',
    ],
]);
Employee List

An employee management page can display:

Employee Code
Name
Email
Department
Designation
Employee Type
Joined Date
Status
Login Status
Actions

Example table:

EMP-001 | Bishal | IT | Developer | Full Time | Active
EMP-002 | Ram    | HR | HR Officer | Full Time | Active
EMP-003 | Hari   | Finance | Accountant | Contract | Inactive
Employee Profile

An employee profile can be divided into several sections:

Employee Information
    Name
    Email
    Employee Code
    Gender
    Date of Birth

Contact Information
    Personal Email
    Personal Phone
    Address

Employment
    Employee Type
    Department
    Designation
    Joined Date
    Probation End
    Employment End

Salary
    Salary
    Salary Type

Emergency Contact
    Name
    Phone
    Relation

Identification
    National ID
    Tax Number

Documents
    Employee Documents
Employee Documents

Employee documents are linked through the employee's user ID.

Example:

$documents = $user->employeeDocuments;

For more information:

Employee Documents

Employee Deactivation

An employee can be deactivated without deleting their user account.

Set:

is_active = false

Example:

$organization->users()->updateExistingPivot(
    $user->id,
    [
        'is_active' => false,
    ]
);

This preserves the employee's historical information.

Disable Employee Login

An employee can remain active while their application login is disabled.

Set:

can_login = false

Example:

$organization->users()->updateExistingPivot(
    $user->id,
    [
        'can_login' => false,
    ]
);

This is useful when an employee exists in the organization but should not access the application.

Employee Deletion

When deleting an employee, consider whether the user account should also be deleted.

The organization_user table has:

$table->foreignId('user_id')
    ->constrained()
    ->cascadeOnDelete();

Therefore, deleting the user will remove the corresponding organization employee relationship.

However, removing an employee from one organization does not necessarily require deleting the user account.

For multi-organization applications, prefer detaching the organization relationship when appropriate:

$organization->users()->detach($user->id);
Multi-Organization Employees

The organization_user table allows a user to belong to multiple organizations.

For example:

User
 |
 +-- Organization A
 |     employee_code = EMP-001
 |     department = IT
 |
 +-- Organization B
       employee_code = DEV-015
       department = Development

Employee information is therefore organization-specific.

This means the same user can have different:

Employee codes
Departments
Designations
Salaries
Employment types
Contact information
Employment dates

for different organizations.

Employee Database Structure

The employee relationship is represented by:

users
│
├── id
├── name
├── email
├── password
└── ...
       │
       │
       ▼
organization_user
│
├── organization_id
├── user_id
├── employee_code
├── gender
├── date_of_birth
├── personal_email
├── personal_phone
├── address
├── city
├── state
├── country
├── postal_code
├── employee_type
├── department
├── designation
├── joined_at
├── probation_ends_at
├── employment_ends_at
├── salary
├── salary_type
├── emergency_contact_name
├── emergency_contact_phone
├── emergency_contact_relation
├── national_id
├── tax_number
├── is_active
└── can_login
Employee Table Constraints

The migration defines a unique combination of:

$table->unique([
    'organization_id',
    'user_id',
]);

This prevents the same user from being attached to the same organization more than once.

Employee codes are also unique within an organization:

$table->unique([
    'organization_id',
    'employee_code',
]);
Employee Security

Employee information may contain sensitive information such as:

Salary
National ID
Tax Number
Personal Phone
Personal Email
Emergency Contact
Documents

Access to employee information should therefore be protected with authentication and authorization.

Do not expose employee information to users who do not have permission to access the organization.

Recommended Employee Permissions

Depending on the application's permission system, employee functionality can be divided into permissions such as:

employees.view
employees.create
employees.update
employees.delete
employees.documents.view
employees.documents.upload
employees.documents.delete
employees.salary.view

The exact permission names depend on the application's authorization implementation.

Employee Search

Employees can be searched using fields such as:

name
email
employee_code
department
designation
employee_type

Example:

$employees = $organization->users()
    ->where(function ($query) use ($search) {
        $query
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWherePivot(
                'employee_code',
                'like',
                "%{$search}%"
            )
            ->orWherePivot(
                'department',
                'like',
                "%{$search}%"
            )
            ->orWherePivot(
                'designation',
                'like',
                "%{$search}%"
            );
    })
    ->get();
Employee Pagination

For large organizations, employees should be paginated.

Example:

$employees = $organization->users()
    ->wherePivot('is_active', true)
    ->paginate(20);

This avoids loading every employee into memory.

Employee Dates

Employee date fields should be handled using the application's date/time configuration.

Important fields:

date_of_birth
joined_at
probation_ends_at
employment_ends_at

See:

Date and Time

Employee Management Workflow

A typical employee workflow is:

Create User
    ↓
Select Organization
    ↓
Enter Employee Information
    ↓
Set Department / Designation
    ↓
Set Employment Information
    ↓
Set Login Permission
    ↓
Save Employee
    ↓
Upload Employee Documents
Employee Summary

The employee system provides:

Organization-based employees
Employee codes
Personal information
Contact information
Address information
Employment types
Departments
Designations
Joining dates
Probation dates
Employment end dates
Salary information
Emergency contacts
National ID
Tax number
Active/inactive status
Login permission
Employee documents
Multi-organization support

The primary employee relationship is:

Organization
    ↓
organization_user
    ↓
User

while employee documents are connected through:

User
    ↓
employee_documents